@extends('layout')

@section('content')
<style>
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .item-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(255, 20, 147, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .item-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(255, 20, 147, 0.2);
        border-color: #ff1493;
    }

    .item-card a {
        text-decoration: none;
    }

    .item-name {
        color: #c71585;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        display: block;
    }

    .item-artist {
        color: #999;
        font-size: 14px;
        margin-bottom: 15px;
        display: block;
    }

    .item-mood {
        background: linear-gradient(135deg, #ffb3d9, #ffc9e3);
        color: #c71585;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .item-card:hover .item-name {
        color: #ff1493;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #ff69b4;
        font-size: 16px;
    }
</style>

<h2 style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <span>🎵 Browse Breakup Songs</span>
    <a href="/" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px; margin: 0;">← Back</a>
</h2>

@if(count($items) > 0)
    <div class="items-grid">
        @foreach($items as $item)
            <a href="/items/{{ $item['id'] }}" style="text-decoration: none;">
                <div class="item-card">
                    <span class="item-name">{{ $item['name'] }}</span>
                    <span class="item-artist">by {{ $item['artist'] }}</span>
                    <span class="item-mood">{{ $item['mood'] }}</span>
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <p>No items available yet. Come back soon!</p>
    </div>
@endif

@endsection