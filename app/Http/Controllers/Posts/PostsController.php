<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
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
    private function validatePostRequest(Request $request): void
    {
        $this->validate($request, [
            'image' => 'required|file|image|mimes:jpeg,png,web,jpg',
        ]);
    }

    /**
     * get all authenticated user's posts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $posts = auth()->user()->posts;

        return response()->json(['posts' => $posts->toArray()]);
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
        $this->validatePostRequest($request);

        $image = $request->file('image');

        $path = Storage::disk('public')->getAdapter()->getPathPrefix() . '/images';
        $hashedName = $image->hashName();

        $imageRealSavePath = $image->move($path, $hashedName)->getRealPath();

        auth()->user()->posts()->create([
            'image_path' => $imageRealSavePath,
        ]);

        return response()->json([
            'created' => true,
            'image_path' => $imageRealSavePath,
        ], 201);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if (! auth()->user()->isOwnerOf($post)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $post->delete();

        return response()->json([
            'deleted' => true,
            'message' => 'Post deleted successfully',
        ], 200);
    }
}
