@extends('layout')

@section('content')
<h2>Edit Song</h2>

@if ($errors->any())
    <ul style="color: #b00020;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form action="{{ route('songs.update', $song->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <p>
        <label>Title</label><br>
        <input type="text" name="title" value="{{ old('title', $song->title) }}" required>
    </p>
    <p>
        <label>Artist</label><br>
        <input type="text" name="artist" value="{{ old('artist', $song->artist) }}" required>
    </p>
    <p>
        <label>Mood</label><br>
        <input type="text" name="mood" value="{{ old('mood', $song->mood) }}">
    </p>
    <p>
        <label>Link</label><br>
        <input type="text" name="link" value="{{ old('link', $song->link) }}" placeholder="https://...">
    </p>
    <p>
        <label>Cover image</label><br>
        @if($song->cover_image)
            <div style="margin-bottom: 1rem;">
                <img src="{{ asset('storage/' . $song->cover_image) }}" alt="" style="width: 300px; height: 200px; object-fit: cover; border-radius: 8px;">
                <div><small>Current cover — upload a new file to replace</small></div>
            </div>
        @endif
        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
        <small>JPEG, PNG, GIF, WebP — max 2MB (optional)</small>
    </p>
    <button type="submit">Update</button>
</form>
<p><a href="{{ route('songs.index') }}">Back</a></p>
@endsection
