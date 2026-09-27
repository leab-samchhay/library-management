<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\BookReturnController;
use App\Http\Controllers\BookTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinePaymentController;
use App\Http\Controllers\FinesController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanDetailController;
use App\Http\Controllers\LoanTranSactionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PublishersController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function (){

    Route::apiResource('category',CategoryController::class);
    Route::apiResource('authors',AuthorsController::class);
    Route::apiResource('publishers',PublishersController::class);
    Route::apiResource('book',BookController::class);
    Route::apiResource('book-copies',BookCopyController::class);
    Route::apiResource('members',MemberController::class);
    Route::apiResource('loans',LoanController::class);
    Route::get('loan-details/late-returns', [LoanDetailController::class, 'getLateReturns']);
    Route::get('loan-details/overdue', [LoanDetailController::class, 'getOverdueLoans']);
    Route::apiResource('loan-details', LoanDetailController::class);
    Route::apiResource('book-returns', BookReturnController::class);
    Route::apiResource('fines', FinesController::class);
    Route::apiResource('fine-payments', FinePaymentController::class);
    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('loanTransaction',LoanTranSactionController::class);
    Route::apiResource('bookTransaction',BookTransactionController::class);
    Route::get('report/monthly-borrower',[DashboardController::class, 'monthlyBorrowers']);
    Route::get('report/getSummary',[DashboardController::class, 'getSummary']);
    Route::get('report/borrow-by-category',[DashboardController::class, 'getBorrowReportByCategory']);
    Route::apiResource('author-books', \App\Http\Controllers\AuthorBookController::class);

    Route::get('current-user',[AuthController::class, 'currentUser']);
    Route::post('logout',[AuthController::class, 'logout']);

    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);

    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);

});

Route::post('login',[AuthController::class, 'login']);



