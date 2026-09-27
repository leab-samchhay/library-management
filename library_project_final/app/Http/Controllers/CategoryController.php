<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category = Category::query();
        $category->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('category_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('id',$search);
            });
        });


        return response() -> json([
            'data'=>$category->latest()->paginate($request->per_page ?? 10),
            'message' => "Get data success "
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
        $validate = $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'required|string',
        ]);

        $category = Category::create($validate);

        return response()->json([
            'data' => $category,
            'message' => 'Create success'

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // $category = Category::findOrFail($category->id);
        return response() ->json([
            'data' => $category,
            'message' => "get data success"
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // $category = Category::findOrFail($category->id);
        $validate = $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'required|string',
        ]);

        $category->update($validate);

        return response()->json([
            'data' => $category,
            'message' => 'update success'

        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // $category = Category::findOrFail($category->id);
        $category->delete();
        return response() -> json([
            'message' => "delete success"
        ]);
    }
}
