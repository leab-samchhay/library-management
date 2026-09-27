<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $book = Book::with('category','publishers');
        $book->when($request->search,function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('isbn', 'like', "%{$search}%")
                         ->orWhere('title', 'like', "%{$search}%");
            });
        });

        $book->when($request->category_id, function ($q, $categoryId) {
            $q->where('category_id', $categoryId);
        });

        return response()->json([
            'data'=>$book->latest()->paginate($request->per_page ?? 10),
            'message'=>'get data success'
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate=$request->validate([
            'category_id'=> 'required|integer|exists:categories,id',
            'publishers_id'=> 'required|integer|exists:publishers,id',
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,giv,svg|max:2048',
            'description' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'language' => 'nullable|string',
            'qty' => 'required|integer',
            'status' => 'boolean'
        ]);

        if($request->hasFile('image')){
            $path = $request->file('image')->store('book','public');
            $validate['image'] = $path;
        }

        $book = Book::create($validate);

        return response() ->json([
            'data'=>$book,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = Book::findOrFail($id);
        return response()->json([
            'data' => $book,
            'message' => 'get data success'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $validate=$request->validate([
            'category_id'=> 'required|integer|exists:categories,id',
            'publishers_id'=> 'required|integer|exists:publishers,id',
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,giv,svg|max:2048',
            'description' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'language' => 'nullable|string',
            'qty' => 'required|integer',
            'status' => 'boolean'
        ]);

        if($request->hasFile('image')){
            if($book->image){
                Storage::disk('public')->delete($book->image);
            }
            $path = $request->file('image')->store('book','public');
            $validate['image'] = $path;
        } else {
            unset($validate['image']);
        }

        $book->update($validate);

        return response() ->json([
            'data'=>$book,
            'message' => 'create success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
