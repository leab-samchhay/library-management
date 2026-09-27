<?php

namespace App\Http\Controllers;

use App\Models\LoanDetail;
use Illuminate\Http\Request;

class LoanDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $loanDetail = LoanDetail::with(['loan', 'bookCopy']);

        $loanDetail->when($request->loan_id, function ($q, $loan_id) {
            $q->where('loan_id', $loan_id);
        });

        $loanDetail->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        return response()->json([
            'data' => $loanDetail->latest()->paginate($request->per_page ?? 10),
            'message' => 'get data success'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'loan_id' => 'required|integer|exists:loans,id',
            'book_copy_id' => 'required|integer|exists:book_copies,id',
            'status' => 'nullable|string|max:255'
        ]);

        $loanDetail = LoanDetail::create($validate);

        return response()->json([
            'data' => $loanDetail,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loanDetail = LoanDetail::with(['loan', 'bookCopy'])->findOrFail($id);

        return response()->json([
            'data' => $loanDetail,
            'message' => 'get data success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loanDetail = LoanDetail::findOrFail($id);

        $validate = $request->validate([
            'loan_id' => 'required|integer|exists:loans,id',
            'book_copy_id' => 'required|integer|exists:book_copies,id',
            'status' => 'nullable|string|max:255'
        ]);

        $loanDetail->update($validate);

        return response()->json([
            'data' => $loanDetail,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loanDetail = LoanDetail::findOrFail($id);
        $loanDetail->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }


    // public function getLateReturns (Request $request){
    //     $lateReturn = LoanDetail::select('loan_details.*')
    //     ->join('loans','loans.id', '=', 'loan_details.loan_id')
    //     ->whereNotNull('loan_details.return_date')
    //     ->whereColumn('loan_details.return_date', '>', 'loans.due_date')
    //     ->with(['loan.member', 'bookCopy'])
    //     ->paginate($request->per_page ?? 10);

    //     return response()->json([
    //         'data' => $lateReturn,
    //         'message' => 'បញ្ជីអ្នកដែលបានសងយឺតថ្ងៃ'
    //     ]);
    // }

    // public function getOverdueLoans (Request $request){
    //     $today = now()->format('Y-m-d');
    //     $overdueLoans = LoanDetail::select('loan_details.*')
    //     ->join('loans', 'loans.id', '=', 'loan_details.loan_id')
    //     ->whereNull('loan_details.return_date')
    //     ->where('loans.due_date', '<', $today)
    //     ->with(['loan.member', 'bookCopy'])
    //     ->paginate($request->per_page ?? 10);

    //     return response()->json([
    //         'data' => $overdueLoans,
    //         'message' => 'បញ្ជីអ្នកដែលមិនទាន់សង និងហួសថ្ងៃកំណត់'
    //     ]);
    // }
}
