<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = [
            ['id'=>1, 'name'=>'Happier', 'artist'=>'Olivia Rodrigo', 'mood'=>'Sad'],
            ['id'=>2, 'name'=>'Let Me Down Slowly', 'artist'=>'Alec Benjamin', 'mood'=>'Heartbroken'],
            ['id'=>3, 'name'=>'Traitor', 'artist'=>'Olivia Rodrigo', 'mood'=>'Betrayed'],
            ['id'=>4, 'name'=>'Someone Like You', 'artist'=>'Adele', 'mood'=>'Moving On'],
            ['id'=>5, 'name'=>'Flowers', 'artist'=>'Miley Cyrus', 'mood'=>'Self Love'],
        ];

        return view('items.index', compact('items'));
    }

    public function show($id)
    {
        $items = [
            ['id'=>1, 'name'=>'Happier', 'artist'=>'Olivia Rodrigo', 'mood'=>'Sad'],
            ['id'=>2, 'name'=>'Let Me Down Slowly', 'artist'=>'Alec Benjamin', 'mood'=>'Heartbroken'],
            ['id'=>3, 'name'=>'Traitor', 'artist'=>'Olivia Rodrigo', 'mood'=>'Betrayed'],
            ['id'=>4, 'name'=>'Someone Like You', 'artist'=>'Adele', 'mood'=>'Moving On'],
            ['id'=>5, 'name'=>'Flowers', 'artist'=>'Miley Cyrus', 'mood'=>'Self Love'],
        ];

        $item = collect($items)->firstWhere('id', $id);

        return view('items.show', compact('item'));
    }
}