<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'classe_description',
        'background_img'

    ];
    public function courses()
    {
        return $this->hasMany(Course::class, 'classe_id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
    public function classeName()
    {
        return $this->classe->titre ?? null;
    }
}
