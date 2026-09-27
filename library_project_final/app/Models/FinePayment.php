<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinePayment extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'fine_id',
        'amount',
        'payment_date',
        'payment_method',
        'received_by',
        'note'
    ];

    public function fine()
    {
        return $this->belongsTo(Fines::class, 'fine_id');
    }
}
