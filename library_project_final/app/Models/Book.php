<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'publishers_id',
        'title',
        'isbn',
        'image',
        'description',
        'publication_year',
        'language',
        'qty',
        'status'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function publishers(){
        return $this->belongsTo(Publishers::class);
    }
    public function copies()
    {
        return $this->hasMany(BookCopy::class, 'book_id', 'id');
    }

    public function authors()
    {
        return $this->belongsToMany(Authors::class, 'author_books', 'book_id', 'authors_id');
    }
}
