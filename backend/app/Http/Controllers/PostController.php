<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    const LOCAL_STORAGE_FOLDER = 'public/images/';

    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function index()
    {
        $all_posts = $this->post->latest()->get();
        return view('posts.index')
            ->with('all_posts', $all_posts);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        #Validate the request
        $request->validate([
            'title' => 'required|min:1|max:50',
            'body' => 'required|min:1|max:1000',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:1048'
        ]);

        //mime -multipurpose internet mail extentions

        #Save the request to the database
        $this->post->user_id = Auth::user()->id;
        //Owner of the post = user who is logged in
        $this->post->title = $request->title; 
        $this->post->body = $request->body; 
        $this->post->image = $this->saveImage($request); 
        $this->post->save();

        #back to Homepage
        return redirect()->route('index');
    }

    private function saveImage($request){
        //Change the name of the image to Currect Time to acoid overwriting.
        $image_name = time() . "." . $request->image->extension();

        //Save the image inside strage/app/public/images
        $request->image->storeAs(self::LOCAL_STORAGE_FOLDER, $image_name);

        return $image_name;


    }

    public function show($id)
    {
        $post = $this->post->findOrFail($id);

        return view('posts.show')
            ->with('post', $post);
    }

    public function edit($id)
    {
        $post = $this->post->findOrFail($id);

        if($post->user->id != Auth::user()->id){
            return redirect()->back();
        }
        return view('posts.edit')->with('post', $post);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|min:1max:50',
            'body' => 'required|min:1|max:1000',
            'image' => 'mimes:jpg,jpeg,png,gif|max:1048'
        ]);

        $post = $this->post->findOrFail($id);
        $post->title = $request->title;
        $post->body = $request->body;

        #If there is new image...
        if($request->image){
            #Delete the previous image from the local storage
            $this->deleteImage($post->image);

            #Move the new image to loval storage
            $post->image = $this->saveImage($request);
            //$post->image = filename.jpg;
        }
        $post->save();

        return redirect()->route('post.show', $id);

    }

    private function deleteImage($image_name){
        $image_path = self::LOCAL_STORAGE_FOLDER . $image_name;
        
        if(Storage::disk('local')->exists($image_path)){
            Storage::disk('local')->delete($image_path);
        }
    }

    

    public function destroy($id)
    {
        $post = $this->post->findOrFail($id);
        $this->deleteImage($post->image);

        $this->post->destroy($id);

        return redirect()->back();
    }
}
