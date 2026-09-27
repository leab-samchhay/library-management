<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bookCopy = BookCopy::with('book.category');
        $bookCopy->when($request->search,function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('barcode',$search);
            });
        });

        $bookCopy->when($request->book_id, function ($q, $bookId) {
            $q->where('book_id', $bookId);
        });

        $bookCopy->when($request->category_name,function ($q, $categoryName) {
            $q->whereHas('book.category',function ($subQuery) use ($categoryName) {
                $subQuery->where('name','like', '%'.$categoryName.'%');
            });
        });

        $bookCopy->when($request->category_id, function ($q, $categoryId) {
            $q->whereHas('book', function ($subQuery) use ($categoryId) {
                $subQuery->where('category_id', $categoryId);
            });
        });

        return response()->json([
            'data'=>$bookCopy->latest()->paginate($request->per_page ?? 10),
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
            'book_id'=> 'required|integer|exists:books,id',
            'barcode'=> 'required|string|max:255|unique:book_copies,barcode',
            'page_number' => 'nullable|integer',
            'shelf_location' => 'nullable|string|max:255',
            'acquisition_date' => 'nullable|date',
            'price' => 'nullable|numeric',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255'
        ]);

        $bookCopy = BookCopy::create($validate);

        return response() ->json([
            'data'=>$bookCopy,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bookCopy = BookCopy::findOrFail($id);
        return response()->json([
            'data' => $bookCopy,
            'message' => 'get data success'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookCopy $bookCopy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bookCopy = BookCopy::findOrFail($id);
        $validate=$request->validate([
            'book_id'=> 'required|integer|exists:books,id',
            'barcode'=> 'required|string|max:255|unique:book_copies,barcode,'.$id,
            'page_number' => 'nullable|integer',
            'shelf_location' => 'nullable|string|max:255',
            'acquisition_date' => 'nullable|date',
            'price' => 'nullable|numeric',
            'condition' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255'
        ]);

        $bookCopy->update($validate);

        return response() ->json([
            'data'=>$bookCopy,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bookCopy = BookCopy::findOrFail($id);
        $bookCopy->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
