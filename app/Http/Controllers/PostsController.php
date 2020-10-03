<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validatePost(Request $request): void
    {
        $this->validate($request, [
            'image' => 'required|file|image|mimes:jpeg,png,web,jpg',
        ]);
    }

    /**
     * create a post and store itself and also it's image in the database
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validatePost($request);

        $image = $request->file('image');

        $path = Storage::disk('public')->getAdapter()->getPathPrefix() . '/images';
        $hashedName = $image->hashName();

        $imageRealSavePath = $image->move($path, $hashedName)->getRealPath();

        Post::create([
            'image_path' => $imageRealSavePath,
        ]);

        return response()->json([
            'created' => true,
            'image_path' => $imageRealSavePath,
        ], 201);
    }

}
