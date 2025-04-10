<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

public function categories()
    {
        return $this->belongsToMany(Categories::class);
    }

public function comments()
    {
        return $this->hasMany(Comments::class);
    }


}