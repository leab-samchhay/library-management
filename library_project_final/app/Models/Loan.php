<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'member_id',
        'staff_id',
        'loan_date',
        'due_date',
        'note',
        'status'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function loanDetails()
    {
        return $this->hasMany(LoanDetail::class, 'loan_id');
    }

}
