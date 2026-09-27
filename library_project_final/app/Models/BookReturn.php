<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_detail_id',
        'returned_date',
        'received_by',
        'note'
    ];

    public function loanDetail()
    {
        return $this->belongsTo(LoanDetail::class);
    }

}
