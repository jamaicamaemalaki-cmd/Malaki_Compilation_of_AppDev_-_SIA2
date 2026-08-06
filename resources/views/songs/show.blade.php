@extends('layout')

@section('content')
<style>
    .detail-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(255, 20, 147, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }

    .cover-image {
        text-align: center;
        margin-bottom: 30px;
    }

    .cover-image img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(255, 20, 147, 0.2);
    }

    .detail-item {
        margin-bottom: 20px;
        padding: 15px;
        background: #fff5f9;
        border-left: 4px solid #ff1493;
        border-radius: 8px;
    }

    .detail-item label {
        display: block;
        color: #c71585;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-item .value {
        color: #333;
        font-size: 16px;
        word-break: break-word;
    }

    .detail-item a {
        color: #ff1493;
        font-weight: 600;
    }

    .detail-item a:hover {
        color: #c71585;
    }

    .actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .actions a {
        flex: 1;
        padding: 12px 24px;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        color: white !important;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(255, 20, 147, 0.3);
    }

    .actions a:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 20, 147, 0.4);
    }
</style>

<div class="detail-container">
    <h2>🎵 Song Details</h2>

    @if($song->cover_image)
        <div class="cover-image" style="margin-bottom: 30px;">
            <img src="{{ asset('storage/' . $song->cover_image) }}" alt="{{ $song->title }}" style="width: 100%; max-width: 600px; height: 400px; border-radius: 12px; object-fit: cover; box-shadow: 0 10px 30px rgba(255, 20, 147, 0.2);">
        </div>
    @endif

    <div class="detail-item">
        <label>Title</label>
        <div class="value">{{ $song->title }}</div>
    </div>

    <div class="detail-item">
        <label>Artist</label>
        <div class="value">{{ $song->artist }}</div>
    </div>

    <div class="detail-item">
        <label>Mood</label>
        <div class="value"><span style="background: #ffb3d9; color: #c71585; padding: 5px 10px; border-radius: 20px; font-weight: 600;">{{ $song->mood }}</span></div>
    </div>

    <div class="detail-item">
        <label>Link</label>
        @if($song->link)
            <div class="value"><a href="{{ $song->link }}" target="_blank" rel="noopener noreferrer">🔗 Listen on {{ parse_url($song->link, PHP_URL_HOST) ?? 'External Link' }}</a></div>
        @else
            <div class="value" style="color: #999;">— No link available</div>
        @endif
    </div>

    <div class="actions">
        <a href="{{ route('songs.edit', $song->id) }}">✏️ Edit</a>
        <a href="{{ route('songs.index') }}">← Back to Songs</a>
    </div>
</div>
@endsection