@extends('layouts.app')

@section('title', 'Home')

@section('content')
    @forelse ($all_posts as $post)
        <div class="mt-2 border border-2 rounded py-3 px-4">
            <a href="{{ route('post.show', $post->id) }}">
                <h2 class="h4">{{ $post->title }}</h2>
            </a>
            <h3 class="h6 text-muted">{{ $post->user->name }}</h3>
            <p class="fw-light mb-0">{{ $post->body }}</p>
            <img src="{{ asset('/storage/images/' . $post->image) }}" alt="{{ $post->image }}" class="rounded d-block mx-auto w-25">

            {{-- If the owner of the post is the Auth user, show edit and delete button. --}}
            @if ($post->user->id === Auth::user()->id)
                <div class="text-end mt-2">
                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-primary btn-sm"><i
                            class="fa-solid fa-pen"></i>Edit</a>

                    <form action="{{ route('post.destroy', $post->id) }}" method="post" class="d-inline">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm"><i
                                class="fa-solid fa-trash-can"></i>Delete</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div style="margin-top: 100px;">
            <h2 class="text-muted text-center">No posts yet.</h2>
            <p class="text-center">
                <a href="{{ route('post.create') }}" class="text-decoration-none">Create a new post</a>
            </p>
        </div>
    @endforelse


@endsection
