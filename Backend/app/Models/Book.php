<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'book';
    protected $fillable = [
            'title',
            'isbn',
            'author_name',
            'publication_year',
            'number_of_copies',
            'category'
    ];

}