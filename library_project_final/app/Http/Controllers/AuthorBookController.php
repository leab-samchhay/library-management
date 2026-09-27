<?php

namespace App\Http\Controllers;

use App\Models\AuthorBook;
use Illuminate\Http\Request;

class AuthorBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = \App\Models\Book::with('authors')->has('authors');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('authors', function($q2) use ($search) {
                      $q2->where('authors_name', 'like', "%{$search}%");
                  });
            });
        }

        $books = $query->paginate($limit);
        
        return response()->json([
            'status' => 'success',
            'data' => $books
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'authors_id' => 'required|array',
            'authors_id.*' => 'exists:authors,id',
            'book_id' => 'required|exists:books,id'
        ]);

        $book = \App\Models\Book::findOrFail($validated['book_id']);
        $book->authors()->syncWithoutDetaching($validated['authors_id']);

        return response()->json([
            'status' => 'success',
            'message' => 'Author Book relationship created successfully',
            'data' => $book->load('authors')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = \App\Models\Book::with('authors')->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                 'book_id' => $book->id,
                 'authors_id' => $book->authors->pluck('id')->toArray()
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'authors_id' => 'required|array',
            'authors_id.*' => 'exists:authors,id',
            'book_id' => 'required|exists:books,id'
        ]);

        $book = \App\Models\Book::findOrFail($id);
        $book->authors()->sync($validated['authors_id']);

        return response()->json([
            'status' => 'success',
            'message' => 'Author Book relationship updated successfully',
            'data' => $book->load('authors')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = \App\Models\Book::findOrFail($id);
        $book->authors()->detach();

        return response()->json([
            'status' => 'success',
            'message' => 'Author Book relationship deleted successfully'
        ], 200);
    }
}
