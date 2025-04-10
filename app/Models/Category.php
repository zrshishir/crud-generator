<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Parent::class);
    }

public function children()
    {
        return $this->hasMany(Children::class);
    }

public function posts()
    {
        return $this->belongsToMany(Posts::class);
    }


}