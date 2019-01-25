<?php

namespace Khaleghi\Media\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Khaleghi\Media\Medium;

class MediaController extends Controller
{
    public function create()
    {
        return view('media::create');
    }

    public function store(Request $request)
    {
        $medium = Medium::create([
            'file' => $request->file('photo'),
        ]);
        return back()->with('url' , $medium->url('sm'));
    }
}
