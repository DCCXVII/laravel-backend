<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->morphToMany(Item::class, 'itemable');
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
