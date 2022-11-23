@extends('layouts.app')

@section('title', 'Edit profile')

@section('content')
    <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="ro mt-2 mb-3">
            <div class="col-4">
                @if ($user->avatar)
                    <img src="{{ asset('/storage/avatars/' . $user->avatar) }}" alt="{{ $user->avatar }}" class="img-thumbnail w-100">
                @else
                    <i class="fa-solid fa-image fa-10x d-block text-center"></i>
                @endif
                <input type="file" name="avatar" class="form-control mt-1" aria-describedby="acatar-info">
                <div class="form-text" id="avatar-info">
                    Acceptable formats: jpeg, jpg, png, gif Only <br>
                    Maximun file size: 1048kb
                </div>
                {{-- Error --}}
                @error('avatar')
                    <p class="text-danger small">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label text-muted">Name</label>
                <input type="text" name="name" id="name" class="form-control"
                    value="{{ old('name', $user->name) }}">
                {{-- Error --}}
                @error('name')
                    <p class="text-danger smalll">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label text-muted">Email Address</label>
                <input type="text" name="email" id="name" class="form-control"
                    value="{{ old('email', $user->email) }}">
                {{-- Error --}}
                @error('name')
                    <p class="text-danger smalll">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn btn-warning px-5">Save</button>
        </div>
    </form>
@endsection
