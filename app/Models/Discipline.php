<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discipline extends Model
{
    use HasFactory;
    public function Classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }
    public function Courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
