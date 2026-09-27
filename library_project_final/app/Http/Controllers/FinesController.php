<?php

namespace App\Http\Controllers;

use App\Models\Fines;
use Illuminate\Http\Request;

class FinesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fines = Fines::with(['return']);

        $fines->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('fine_type', 'like', "%{$search}%")
                         ->orWhere('amount', 'like', "%{$search}%");
            });
        });

        $fines->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        return response()->json([
            'data' => $fines->latest()->paginate($request->per_page ?? 10),
            'message' => 'get data success'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'return_id' => 'required|integer|exists:book_returns,id',
            'fine_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:255'
        ]);

        $fine = Fines::create($validate);

        return response()->json([
            'data' => $fine,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fine = Fines::with(['loanDetail'])->findOrFail($id);

        return response()->json([
            'data' => $fine,
            'message' => 'get data success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $fine = Fines::findOrFail($id);

        $validate = $request->validate([
            'return_id' => 'required|integer|exists:book_returns,id',
            'fine_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:255'
        ]);

        $fine->update($validate);

        return response()->json([
            'data' => $fine,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fine = Fines::findOrFail($id);
        $fine->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
