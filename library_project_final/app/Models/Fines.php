<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fines extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'return_id',
        'fine_type',
        'amount',
        'status'
    ];

    public function return (){
        return $this->belongsTo(BookReturn::class);
    }
}
