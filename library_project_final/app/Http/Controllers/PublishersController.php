<?php

namespace App\Http\Controllers;

use App\Models\Publishers;
use Illuminate\Http\Request;

class PublishersController extends Controller
{
    public function index(Request $request)
    {
        $publishers = Publishers::query()
            ->when($request->search, function ($q, $search) {
                $q->where('publishers_name', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'data' => $publishers,
            'message' => 'get data success'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'publishers_name' => 'required|string|max:255',
            'phone' => 'nullable|string|min:2|max:50',
            'email' => 'nullable|string|min:2|max:100',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable',
        ]);

        $publisher = Publishers::create($validated);

        return response()->json([
            'data' => $publisher,
            'message' => 'create success'
        ], 201);
    }

    public function show($id)
    {
        $publisher = Publishers::findOrFail($id);

        return response()->json([
            'data' => $publisher,
            'message' => 'get data success'
        ]);
    }

    public function update(Request $request, $id)
    {
        $publisher = Publishers::findOrFail($id);

        $validated = $request->validate([
            'publishers_name' => 'required|string|max:255',
            'phone' => 'nullable|string|min:2|max:50',
            'email' => 'nullable|string|min:2|max:100',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable',
        ]);

        $publisher->update($validated);

        return response()->json([
            'data' => $publisher,
            'message' => 'update success'
        ]);
    }

    public function destroy($id)
    {
        $publisher = Publishers::findOrFail($id);
        $publisher->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
