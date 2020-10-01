<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request): void
    {
        $this->validate($request, [
            'image' => 'required|file|image|mimes:jpeg,png,web,jpg',
        ]);
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        $image = $request->file('image');

        $path = Storage::disk('public')->getAdapter()->getPathPrefix() . '/images';
        $hashedName = $image->hashName();

        $image->move($path, $hashedName);

        $imageRealSavePath = realpath($path . "\\" . $hashedName);

        Post::create([
            'image_path' => $imageRealSavePath,
        ]);

        return response()->json([
            'created' => true,
            'image_path' => $imageRealSavePath,
        ], 201);
    }
}
