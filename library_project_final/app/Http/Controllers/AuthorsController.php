<?php

namespace App\Http\Controllers;

use App\Models\Authors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authors = Authors::query();
        $authors->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('authors_name', 'like', '%' .$search. '%');
            });
        });

        return response() -> json([
            'data' => $authors->latest()->paginate($request->per_page ?? 10),
            'message' => "get data success"
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
            'authors_name' => 'required|string|max:255',
            'biography' => 'nullable|string'
        ]);

        $authors = Authors::create($validate);

        return response() -> json([
            'data' => $authors,
            'message' => 'crate success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $authors = Authors::findOrFail($id);
        return response()->json([
            'data'=>$authors,
            'message' => 'get data success'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Authors $authors)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $authors = Authors::findOrFail($id);
        $validate = $request->validate([
            'authors_name' => 'required|string|max:255',
            'biography' => 'string'
        ]);

        $authors->update($validate);

        return response() -> json([
            'data' => $authors,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $authors = Authors::findOrFail($id);
        $authors->delete();
        return response() ->json([
            'message' => 'delete success'
        ]);
    }
}
