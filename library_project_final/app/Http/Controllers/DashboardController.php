<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\BookReturn;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Illuminate\Support\years;

class DashboardController extends Controller
{
    public function monthlyBorrowers(Request $request){
        $report = Loan::select(
            DB::raw('YEAR(loan_date) as year'),
            DB::raw('MONTH(loan_date) as month'),
            DB::raw('COUNT(DISTINCT member_id) as total_member_borrowers'),
            DB::raw('COUNT(id) as total_loans')

        )
        ->groupBy('year','month')
        ->orderBy('year','asc')
        ->orderBy('month','asc')
        ->get();

        $formattedData = $report->map(function ($item) {
            $monthName = date("M", mktime(0, 0, 0, $item->month, 10)); // បំប្លែងលេខខែ ទៅជាអក្សរ (ឧ. Jan, Feb)
            return [
                'label' => $monthName . ' ' . $item->year,
                'total_member_borrowers' => $item->total_member_borrowers,
                'total_loans' => $item->total_loans,
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'message' => 'Monthly borrowers report generated successfully'
        ]);
    }

    public function getSummary()
    {
        $totalUsers = User::count();
        $totalMembers = Member::count();
        $totalBooks = BookCopy::count();
        $totalSpent = BookCopy::sum('price');

        // --- Books Info ---
        $goodConditionCount = BookCopy::where('condition', 'Good')->count();
        $damagedCount = BookCopy::where('condition', 'Damaged')->count();
        $lostCount = BookCopy::where('condition', 'Lost')->orWhere('status', 'Lost')->count();

        // --- Loan Info ---
        $booksBorrowed = LoanDetail::count();
        $activeBorrowers = BookCopy::where('status', 'Borrowed')->count();

        // --- Return Info ---
        $booksReturned = BookReturn::count();
        $membersReturned = Loan::where('status','0')->distinct('member_id')->count('member_id');

        return response()->json([
            'data' => [
                'total_users' => $totalUsers,
                'total_members' => $totalMembers,
                'total_books' => $totalBooks,
                'total_spent' => $totalSpent ?? 0,

                // Widget Info
                'books_info' => [
                    'good_condition' => $goodConditionCount,
                    'damaged' => $damagedCount,
                    'lost' => $lostCount,
                ],
                'loan_info' => [
                    'books_borrowed' => $booksBorrowed,
                    'active_borrowers' => $activeBorrowers,
                ],
                'return_info' => [
                    'books_returned' => $booksReturned,
                    'members_returned' => $membersReturned,
                ]
            ],
            'message' => 'Dashboard summary fetched successfully'
        ]);
    }

    public function getBorrowReportByCategory()
    {
        $report = DB::table('categories')
            ->join('books', 'categories.id', '=', 'books.category_id')
            ->join('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->join('loan_details', 'book_copies.id', '=', 'loan_details.book_copy_id')
            ->select(
                'categories.category_name',
                'books.title',
                DB::raw('COUNT(loan_details.id) as borrow_count')
            )
            ->groupBy('categories.id', 'categories.category_name', 'books.id', 'books.title')
            ->having('borrow_count', '>', 0)
            ->get();

        $formattedData = [];
        $grouped = $report->groupBy('category_name');

        foreach ($grouped as $categoryName => $books) {
            $children = [];
            $categoryTotal = 0;

            foreach ($books as $book) {
                $children[] = [
                    'x' => $book->title,
                    'y' => (int) $book->borrow_count
                ];
                $categoryTotal += $book->borrow_count;
            }

            $formattedData[] = [
                'x' => $categoryName,
                'y' => $categoryTotal,
                'children' => $children
            ];
        }

        return response()->json([
            'data' => $formattedData,
            'message' => 'Book borrow report by category generated successfully'
        ]);
    }
}
