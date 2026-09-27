<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'member_id',
        'book_copy_id',
        'reservation_date',
        'expiry_date',
        'status',
        'note'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }
}
