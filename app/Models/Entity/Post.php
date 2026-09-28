<?php

namespace App\Models\Entity;

use App\Models\Tables\PostTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends PostTable
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];
}
