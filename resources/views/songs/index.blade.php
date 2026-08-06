@extends('layout')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(255, 20, 147, 0.1);
        margin-bottom: 25px;
    }

    .search-box form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-box input {
        padding: 10px 15px;
        border: 2px solid #ffb3d9;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: #ff1493;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(255, 20, 147, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th {
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    table tr:hover {
        background: #fff5f9;
    }

    table img {
        border-radius: 8px;
        object-fit: cover;
    }

    .action-btns {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .action-btns form {
        display: inline;
    }

    .action-btns button {
        padding: 6px 12px;
        background: #ff6b9d;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .action-btns button:hover {
        background: #c71585;
    }
</style>

<div class="page-header">
    <h2>🎵 Songs Collection</h2>
    <div style="display: flex; gap: 10px;">
        <a href="/" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">← Back</a>
        <a href="{{ route('songs.create') }}" class="btn">➕ Add New Song</a>
    </div>
</div>

<div class="search-box">
    <form method="GET" action="{{ route('songs.index') }}">
        <label for="mood" style="font-weight: 600; color: #c71585;">Filter by mood:</label>
        <input type="text" id="mood" name="mood" value="{{ request('mood') }}" placeholder="e.g. Sad">
        <button type="submit" class="btn" style="padding: 10px 20px; font-size: 14px;">🔍 Search</button>
        @if(request()->filled('mood'))
            <a href="{{ route('songs.index') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">Clear</a>
        @endif
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Cover</th>
                <th>Title</th>
                <th>Artist</th>
                <th>Mood</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($songs as $song)
            <tr>
                <td>
                    @if($song->cover_image)
                        <img src="{{ asset('storage/' . $song->cover_image) }}" alt="Cover" style="width: 120px; height: 80px; border-radius: 8px; object-fit: cover;">
                    @else
                        —
                    @endif
                </td>
                <td><strong>{{ $song->title }}</strong></td>
                <td>{{ $song->artist }}</td>
                <td><span style="background: #ffb3d9; color: #c71585; padding: 5px 10px; border-radius: 20px; font-size: 13px; font-weight: 600;">{{ $song->mood }}</span></td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('songs.show', $song->id) }}" style="color: #ff1493; font-weight: 600;">View</a>
                        <a href="{{ route('songs.edit', $song->id) }}" style="color: #ff69b4; font-weight: 600;">Edit</a>
                        <form action="{{ route('songs.destroy', $song->id) }}" method="POST" style="display:inline;" onclick="return confirm('Delete this song?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #ff69b4; font-size: 16px;">No songs found. <a href="{{ route('songs.create') }}">Add one now!</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 25px; display: flex; justify-content: center;">
    {{ $songs->links() }}
</div>
@endsection
