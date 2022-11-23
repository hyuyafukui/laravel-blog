<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    const LOCAL_STORAGE_FOLDER = 'public/avatars/';

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function show()
    {
        $user = $this->user->find(Auth::user()->id);

        return view('users.show')
            ->with('user', $user);
    }

    public function edit()
    {
        $user = $this->user->find(Auth::user()->id);
        
        return view('users.edit')
            ->with('user', $user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|min:1|max:50',
            'email' => 'required|email|max:50|' . Rule::unique('users')->ignore(Auth::user()->id),
            'avatar' => 'mimes:jpg,jpeg,png,gif|max:1048'
        ]);
        $user = $this->user->find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;

        #if the user uploaded an avatar
        if($request->avatar){
            //If the user currently has avatar, delete it from local storage
            if($user->avatar){
                $this->deleteAvatar($user->avatar);
            }

            //Move the new image to local storage
            $user->avatar = $this->saveAvatar($request);
        }
        $user->save();

        return redirect()->route('profile.show');
    }

    public function saveAvatar($request)
    {
        #Rename the image to the current time to avoid overwriting
        $avatar_name = time(). "." . $request->avatar->extension();
        //$avatar_name = 'filename.jpg';

        #save the image inside the storage/app/public/avatars
        $request->avatar->storeAs(self::LOCAL_STORAGE_FOLDER, $avatar_name);

        return $avatar_name;
    }

    public function deleteAvatar($avatar_name)
    {
        $avatar_path = self::LOCAL_STORAGE_FOLDER . $avatar_name;
        //$avatar_path = '/public/avatars/filename.jpg';

        if(Storage::disk('local')->exists($avatar_path)){
            Storage::disk('local')->delete($avatar_path);
        }
    }
}
