<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Bejegyzések listázása.
     */
    public function index()
    {
        $posts = Post::with('author')->latest()->get();

        return response()->json([
            'success' => true,
            'posts' => $posts,
        ]);
    }

    /**
     * Egyedi bejegyzés megtekintése.
     */
    public function show($id)
    {
        $post = Post::with('author')->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Bejegyzés nem található.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'post' => $post,
        ]);
    }

    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $authorId = Auth::guard('admin')->id();
            if (is_null($authorId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Az admin azonosító nem található!',
                ], 500);
            }

            $post = Post::create([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'author_id' => $validatedData['author_id'] = Auth::guard('admin')->id(),
                'slug' => Str::slug($validatedData['title']),
                'excerpt' => Str::limit($validatedData['content'], 100),
                'status' => 'draft',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'A poszt sikeresen létrejött!',
                'post' => $post,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Érvénytelen adatok.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a poszt létrehozása során.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bejegyzés frissítése.
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Bejegyzés nem található!',
            ], 404);
        }

        if ($post->author_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Hozzáférés megtagadva!',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bejegyzés sikeresen frissítve!',
            'post' => $post,
        ]);
    }

    /**
     * Bejegyzés törlése.
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Bejegyzés nem található!',
            ], 404);
        }

        if ($post->author_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Hozzáférés megtagadva!',
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bejegyzés sikeresen törölve!',
        ]);
    }
}
