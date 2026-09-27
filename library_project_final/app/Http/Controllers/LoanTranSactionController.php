<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class LoanTranSactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'            => 'required|exists:members,id',
            'staff_id'             => 'required|string',
            'loan_date'            => 'required|date',
            'due_date'             => 'required|date|after_or_equal:loan_date',
            'note'                 => 'nullable|string',
            'status'               => 'nullable|string',
            'books'                => 'required|array|min:1',
            'books.*.book_copy_id' => 'required|exists:book_copies,id',
            'books.*.return_date'  => 'nullable|date',
            'books.*.status'       => 'nullable|string',
        ]);

        try {

            $loan = DB::transaction(function () use ($validated) {

                $newLoan = Loan::create([
                    'member_id' => $validated['member_id'],
                    'staff_id'  => $validated['staff_id'],
                    'loan_date' => $validated['loan_date'],
                    'due_date'  => $validated['due_date'],
                    'note'      => $validated['note'] ?? null,
                    'status'    => $validated['status'] ?? 'Active',
                ]);

                foreach ($validated['books'] as $book) {
                    LoanDetail::create([
                        'loan_id'      => $newLoan->id,
                        'book_copy_id' => $book['book_copy_id'],
                        'return_date'  => $book['return_date'] ?? null,
                        'status'       => $book['status'] ?? 'Borrowed',
                    ]);

                    BookCopy::where('id', $book['book_copy_id'])->update(['status' => 'Borrowed']);
                }

                return $newLoan;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Loans succes !!',
                'data'    => $loan->load('loanDetails')
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'ការបញ្ចូលទិន្នន័យបរាជ័យ៖ ' . $e->getMessage()
            ], 500);
        }
    }
}
