<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'discipline_description',
        'background_img'

    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'discipline_id');
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}
