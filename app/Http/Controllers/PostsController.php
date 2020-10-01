<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function store(Request $request)
    {
        $image = $request->file('image');

        $path = Storage::disk('public')->getAdapter()->getPathPrefix() . '/images';
        $hashedName = $image->hashName();

        $image->move($path, $hashedName);
    }
}
