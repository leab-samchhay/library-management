<?php

namespace App\Http\Controllers;

use App\Models\FinePayment;
use Illuminate\Http\Request;

class FinePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payments = FinePayment::with(['fine']);

        $payments->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('payment_method', 'like', "%{$search}%")
                         ->orWhere('amount', 'like', "%{$search}%")
                         ->orWhere('received_by', 'like', "%{$search}%")
                         ->orWhereHas('fine', function($query) use ($search) {
                            $query->where('fine_type', 'like', "%{$search}%");
                         });
            });
        });

        return response()->json([
            'data' => $payments->latest()->paginate($request->per_page ?? 10),
            'message' => 'get data success'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'fine_id' => 'required|integer|exists:fines,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'note' => 'nullable|string'
        ]);

        $payment = FinePayment::create($validate);

        $fine = \App\Models\Fines::findOrFail($request->fine_id);
        $totalPaid = \App\Models\FinePayment::where('fine_id', $fine->id)->sum('amount');
        if ($totalPaid >= $fine->amount) {
            $fine->update(['status' => 'Paid']);
        } else {
            $fine->update(['status' => 'Unpaid']);
        }

        return response()->json([
            'data' => $payment,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = FinePayment::with(['fine'])->findOrFail($id);

        return response()->json([
            'data' => $payment,
            'message' => 'get data success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $payment = FinePayment::findOrFail($id);

        $validate = $request->validate([
            'fine_id' => 'required|integer|exists:fines,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'note' => 'nullable|string'
        ]);

        $payment->update($validate);

        $fine = \App\Models\Fines::findOrFail($request->fine_id);
        $totalPaid = \App\Models\FinePayment::where('fine_id', $fine->id)->sum('amount');
        if ($totalPaid >= $fine->amount) {
            $fine->update(['status' => 'Paid']);
        } else {
            $fine->update(['status' => 'Unpaid']);
        }

        return response()->json([
            'data' => $payment,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $payment = FinePayment::findOrFail($id);
        $fineId = $payment->fine_id;
        $payment->delete();

        $fine = \App\Models\Fines::findOrFail($fineId);
        $totalPaid = \App\Models\FinePayment::where('fine_id', $fine->id)->sum('amount');
        if ($totalPaid >= $fine->amount) {
            $fine->update(['status' => 'Paid']);
        } else {
            $fine->update(['status' => 'Unpaid']);
        }

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
