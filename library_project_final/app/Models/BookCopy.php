<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'book_id',
        'barcode',
        'page_number',
        'shelf_location',
        'acquisition_date',
        'price',
        'condition',
        'status'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
