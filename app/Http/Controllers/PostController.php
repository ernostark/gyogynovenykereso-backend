<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{

    public function index()
    {
        try {
            if (!auth('admin')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nincs jogosultság!',
                ], 401);
            }

            $posts = Post::with('category', 'author')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'posts' => $posts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a bejegyzések lekérésekor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
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
                'category_id' => 'nullable|exists:categories,id',
                'status' => 'required|in:draft,published,archived',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'diseases' => 'nullable',
            ]);

            $authorId = Auth::guard('admin')->id();
            if (is_null($authorId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Az admin azonosító nem található!',
                ], 500);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('images', 'public');
                $validatedData['image_path'] = $imagePath;
            }

            $publishedAt = $validatedData['status'] === 'published' ? now() : null;

            $diseases = $request->input('diseases');

            if (is_string($diseases)) {
                $diseases = json_decode($diseases, true);
            }

            $post = Post::create([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'author_id' => $validatedData['author_id'] = Auth::guard('admin')->id(),
                'slug' => Str::slug($validatedData['title']),
                'excerpt' => Str::limit($validatedData['content'], 100),
                'category_id' => $validatedData['category_id'] ?? null,
                'status' => $validatedData['status'] ?? 'draft',
                'image_path' => $validatedData['image_path'] ?? null,
                'published_at' => $publishedAt,
                'diseases' => $diseases,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'A poszt sikeresen létrejött!',
                'post' => $post,
                'diseases' => $post->diseases,
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
    public function update(Request $request, $id)
    {

        try {
            if (!auth('admin')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nincs jogosultság!',
                ], 401);
            }

            $post = Post::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category_id' => 'nullable|exists:categories,id',
                'status' => 'required|in:draft,published,archived',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'diseases' => 'nullable|string',
            ]);

            if (isset($validatedData['image_path'])) {
                $post->update(['image_path' => $validatedData['image_path']]);
            }

            if ($request->hasFile('image')) {
                if ($post->image_path) {
                    FacadesStorage::disk('public')->delete($post->image_path);
                }

                $image = $request->file('image');
                $imagePath = $image->store('images', 'public');
                $validatedData['image_path'] = $imagePath;
            }

            if ($validatedData['status'] === 'published' && !$post->published_at) {
                $validatedData['published_at'] = now();
            }

            $post->update($validatedData);
            $validatedData['diseases'] = json_encode($validatedData['diseases'] ?? []);

            return response()->json([
                'success' => true,
                'message' => 'A bejegyzés sikeresen frissítve!',
                'post' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a bejegyzés frissítése során.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            if (!auth('admin')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nincs jogosultság!',
                ], 401);
            }

            $post = Post::findOrFail($id);

            if ($post->image_path) {
                FacadesStorage::disk('public')->delete($post->image_path);
            }

            $post->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'A bejegyzés sikeresen törölve!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a bejegyzés törlése során.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchByDiseases(Request $request)
    {
        $searchTerm = $request->input('diseases');

        if (empty($searchTerm)) {
            return response()->json([
                'message' => 'Kérlek adj meg keresési feltételt',
                'posts' => []
            ]);
        }

        try {
            $searchTerms = json_decode($searchTerm, true);

            if (!is_array($searchTerms)) {
                $searchTerms = [$searchTerm];
            }

            $posts = Post::where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $disease) {
                    $query->orWhereRaw('JSON_CONTAINS(diseases, ?)', [json_encode($disease)]);
                }
            })
                ->with(['category', 'author'])
                ->get();

            return response()->json([
                'message' => 'Keresés sikeres',
                'posts' => $posts,
            ]);
        } catch (\Exception $e) {
            Log::error('Hiba a keresés során: ' . $e->getMessage());
            return response()->json([
                'message' => 'Hiba történt a keresés során',
                'posts' => []
            ], 500);
        }
    }
}
