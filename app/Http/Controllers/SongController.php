<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSongRequest;
use App\Http\Requests\UpdateSongRequest;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    public function index(Request $request)
    {
        $songs = Song::query()
            ->when($request->filled('mood'), function ($query) use ($request) {
                $mood = addcslashes((string) $request->input('mood'), '%_\\');

                $query->where('mood', 'like', '%'.$mood.'%');
            })
            ->orderBy('title')
            ->paginate(5)
            ->withQueryString();

        return view('songs.index', compact('songs'));
    }

    public function create()
    {
        return view('songs.create');
    }

    public function store(StoreSongRequest $request)
    {
        $data = $request->safe()->except('cover_image');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        Song::create($data);

        return redirect()->route('songs.index');
    }

    public function show(Song $song)
    {
        return view('songs.show', compact('song'));
    }

    public function edit(Song $song)
    {
        return view('songs.edit', compact('song'));
    }

    public function update(UpdateSongRequest $request, Song $song)
    {
        $data = $request->safe()->except('cover_image');

        if ($request->hasFile('cover_image')) {
            if ($song->cover_image) {
                Storage::disk('public')->delete($song->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $song->update($data);

        return redirect()->route('songs.index');
    }

    public function destroy(Song $song)
    {
        if ($song->cover_image) {
            Storage::disk('public')->delete($song->cover_image);
        }

        $song->delete();

        return redirect()->route('songs.index');
    }
}
