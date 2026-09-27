<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reservations = Reservation::with(['member', 'bookCopy']);

        $reservations->when($request->search, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('member', function($query) use ($search) {
                             $query->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('member_code', 'like', "%{$search}%");
                         })
                         ->orWhere('status', 'like', "%{$search}%")
                         ->orWhere('note', 'like', "%{$search}%");
            });
        });

        $reservations->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        return response()->json([
            'data' => $reservations->latest()->paginate($request->per_page ?? 10),
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
            'book_copy_id' => 'required|integer|exists:book_copies,id',
            'reservation_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:reservation_date',
            'status' => 'nullable|string|max:255',
            'note' => 'nullable|string'
        ]);

        $reservation = Reservation::create($validate);

        return response()->json([
            'data' => $reservation,
            'message' => 'create success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reservation = Reservation::with(['member', 'bookCopy'])->findOrFail($id);

        return response()->json([
            'data' => $reservation,
            'message' => 'get data success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validate = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'book_copy_id' => 'required|integer|exists:book_copies,id',
            'reservation_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:reservation_date',
            'status' => 'nullable|string|max:255',
            'note' => 'nullable|string'
        ]);

        $reservation->update($validate);

        return response()->json([
            'data' => $reservation,
            'message' => 'update success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'message' => 'delete success'
        ]);
    }
}
