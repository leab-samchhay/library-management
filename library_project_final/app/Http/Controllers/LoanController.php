<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $loan = Loan::with(['member']);

        $loan->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('member', function($query) use ($search) {
                    $query->where('member_code', $search)
                          ->orWhere('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        });

        $loan->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        return response()->json([
            'data' => $loan->latest()->paginate($request->per_page ?? 10),
            'message' => 'get data success'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'staff_id' => 'nullable|string',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'note' => 'nullable|string',
            'status' => 'nullable|string|max:255'
        ]);

        $loan = Loan::create($validate);

        return response()->json([
            'data' => $loan,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loan = Loan::with(['member'])->findOrFail($id);

        return response()->json([
            'data' => $loan,
            'message' => 'get data success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $validate = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'staff_id' => 'nullable|string',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'note' => 'nullable|string',
            'status' => 'nullable|string|max:255'
        ]);

        $loan->update($validate);

        return response()->json([
            'data' => $loan,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
