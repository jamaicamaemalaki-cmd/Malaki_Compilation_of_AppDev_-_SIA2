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

    .detail-item {
        margin-bottom: 25px;
        padding: 20px;
        background: #fff5f9;
        border-left: 4px solid #ff1493;
        border-radius: 8px;
    }

    .detail-item label {
        display: block;
        color: #c71585;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-item .value {
        color: #333;
        font-size: 18px;
        font-weight: 500;
    }

    .back-link {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        color: white !important;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(255, 20, 147, 0.3);
        text-decoration: none;
    }

    .back-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 20, 147, 0.4);
    }
</style>

<div class="detail-container">
    <h2>🎵 Song Details</h2>

    <div class="detail-item">
        <label>Title</label>
        <div class="value">{{ $item['name'] }}</div>
    </div>

    <div class="detail-item">
        <label>Artist</label>
        <div class="value">{{ $item['artist'] }}</div>
    </div>

    <div class="detail-item">
        <label>Mood</label>
        <div class="value"><span style="background: #ffb3d9; color: #c71585; padding: 8px 12px; border-radius: 20px; font-weight: 600;">{{ $item['mood'] }}</span></div>
    </div>

    <a href="/items" class="back-link">← Back to Items</a>
</div>
@endsection