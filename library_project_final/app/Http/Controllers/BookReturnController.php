<?php

namespace App\Http\Controllers;

use App\Models\BookReturn;
use Illuminate\Http\Request;

class BookReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bookReturn = BookReturn::with(['loanDetail']);

        $bookReturn->when($request->loan_detail_id, function ($q, $loan_detail_id) {
            $q->where('loan_detail_id', $loan_detail_id);
        });

        return response()->json([
            'data' => $bookReturn->latest()->paginate($request->per_page ?? 10),
            'message' => 'get data success'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'loan_detail_id' => 'required|integer|exists:loan_details,id',
            'returned_date' => 'required|date',
            'received_by' => 'required|string',
            'note' => 'nullable|string',
            'fine_amount' => 'nullable|numeric|min:0',
            'fine_type' => 'nullable|string|max:255',
            'book_condition' => 'nullable|string|in:Good,Damaged,Lost'
        ]);

        $bookReturn = BookReturn::create($request->except(['fine_amount', 'fine_type', 'book_condition']));

        if ($request->has('fine_amount') && $request->fine_amount > 0) {
            \App\Models\Fines::create([
                'return_id' => $bookReturn->id,
                'fine_type' => $request->fine_type ?? 'Late Return',
                'amount' => $request->fine_amount,
                'status' => 'Unpaid'
            ]);
        }

        $loanDetail = \App\Models\LoanDetail::find($request->loan_detail_id);
        if ($loanDetail) {
            $bookCopy = \App\Models\BookCopy::find($loanDetail->book_copy_id);
            $condition = $request->book_condition ?? 'Good';

            if ($condition === 'Lost') {
                $loanDetail->status = 'Returned (Lost)';
                if ($bookCopy) {
                    $bookCopy->status = 'Inactive';
                    $bookCopy->condition = 'Lost';
                }
            } elseif ($condition === 'Damaged') {
                $loanDetail->status = 'Returned (Damaged)';
                if ($bookCopy) {
                    $bookCopy->status = 'Damaged';
                    $bookCopy->condition = 'Damaged';
                }
            } else {
                $loanDetail->status = 'Returned';
                if ($bookCopy) {
                    $bookCopy->status = 'Available';
                    $bookCopy->condition = 'Good';
                }
            }
            
            $loanDetail->save();
            if ($bookCopy) {
                $bookCopy->save();
            }
        }

        return response()->json([
            'data' => $bookReturn,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bookReturn = BookReturn::with(['loanDetail'])->findOrFail($id);

        return response()->json([
            'data' => $bookReturn,
            'message' => 'get data success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bookReturn = BookReturn::findOrFail($id);

        $validate = $request->validate([
            'loan_detail_id' => 'required|integer|exists:loan_details,id',
            'returned_date' => 'required|date',
            'received_by' => 'required|string',
            'note' => 'nullable|string'
        ]);

        $bookReturn->update($validate);

        return response()->json([
            'data' => $bookReturn,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bookReturn = BookReturn::findOrFail($id);
        $bookReturn->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
