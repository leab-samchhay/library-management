<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorBook extends Model
{
    protected $fillable = ['authors_id', 'book_id'];

    public function author()
    {
        return $this->belongsTo(Authors::class, 'authors_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
