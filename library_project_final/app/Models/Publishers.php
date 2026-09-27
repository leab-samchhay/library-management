<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publishers extends Model
{
    use SoftDeletes;
    protected $fillable = ['publishers_name','phone','email','address','status'];
}
