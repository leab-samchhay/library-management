<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_id',
        'book_copy_id',
        'status'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }
}
