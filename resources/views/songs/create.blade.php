@extends('layout')

@section('content')
<style>
    .form-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(255, 20, 147, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #c71585;
        font-weight: 600;
        font-size: 14px;
    }

    .form-group input[type="text"],
    .form-group input[type="file"] {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #ffb3d9;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #ff1493;
        box-shadow: 0 0 0 3px rgba(255, 20, 147, 0.1);
    }

    .form-group small {
        display: block;
        margin-top: 5px;
        color: #999;
        font-size: 12px;
    }

    .error-box {
        background: #ffe0e0;
        border: 2px solid #ff6b9d;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 25px;
        color: #c71585;
    }

    .error-box ul {
        list-style: none;
        padding: 0;
    }

    .error-box li {
        padding: 5px 0;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .form-actions button {
        flex: 1;
        padding: 14px 24px;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(255, 20, 147, 0.3);
    }

    .form-actions button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 20, 147, 0.4);
    }

    .back-link {
        display: inline-block;
        margin-top: 15px;
        color: #ff1493;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .back-link:hover {
        color: #c71585;
    }
</style>

<div class="form-container">
    <h2>➕ Add New Song</h2>

    @if ($errors->any())
        <div class="error-box">
            <strong>Oops! Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('songs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title">Song Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="artist">Artist *</label>
            <input type="text" id="artist" name="artist" value="{{ old('artist') }}" required>
        </div>

        <div class="form-group">
            <label for="mood">Mood</label>
            <input type="text" id="mood" name="mood" value="{{ old('mood') }}" placeholder="e.g. Sad, Angry, Healing">
        </div>

        <div class="form-group">
            <label for="link">Song Link</label>
            <input type="text" id="link" name="link" value="{{ old('link') }}" placeholder="https://example.com/song">
        </div>

        <div class="form-group">
            <label for="cover_image">Cover Image</label>
            <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
            <small>📸 Supported: JPEG, PNG, GIF, WebP (max 2MB)</small>
        </div>

        <div class="form-actions">
            <button type="submit">💾 Save Song</button>
        </div>
    </form>

    <a href="{{ route('songs.index') }}" class="back-link">← Back to Songs</a>
</div>
@endsection
