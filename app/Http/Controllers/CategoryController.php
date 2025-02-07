<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategória sikeresen létrehozva!',
            'category' => $category,
        ], 201);
    }

    public function index()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategória törölve!',
        ]);
    }

    public function getPosts($id)
    {
        try {
            $category = Category::findOrFail($id);
            $posts = Post::where('category_id', $id)
                ->where('status', 'published')
                ->with(['author'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'category' => $category,
                'posts' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hiba történt',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
