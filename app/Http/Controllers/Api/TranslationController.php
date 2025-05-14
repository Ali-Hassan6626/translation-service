<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Translation;

class TranslationController extends Controller
{
    // GET /api/translations?search=...
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Translation::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('locale', 'like', "%{$search}%")
                    ->orWhere('tag', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderByDesc('id')->paginate(100));
    }


    // POST /api/translations
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255',
            'content' => 'required|string',
            'locale' => 'required|string|max:10',
            'tag' => 'nullable|string|max:50',
        ]);

        $translation = Translation::create($data);

        return response()->json($translation, 201);
    }

    // PUT /api/translations/{id}
    public function update(Request $request, $id)
    {
        $translation = Translation::findOrFail($id);

        $data = $request->validate([
            'key' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'locale' => 'sometimes|required|string|max:10',
            'tag' => 'nullable|string|max:50',
        ]);

        $translation->update($data);

        return response()->json($translation);
    }

    // GET /api/translations/export/{locale}
    public function export($locale)
    {
        $translations = Translation::where('locale', $locale)
            ->get()
            ->pluck('content', 'key'); // key => content

        return response()->json($translations);
    }
}
