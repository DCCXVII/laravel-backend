<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'niveau',
        'price',
        'coach_id',
        'classe_id',
        'discipline_id',
        'thumbnail_image',
        'views_number',
        'sells_number',
        'teaser_url',
        'courses_number',
        'status',
    ];


    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class);
    }

    public function disciplineName()
    {
        return $this->discipline->titre ?? null;
    }

    public function classeName()
    {
        return $this->classe->titre ?? null;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['discipline_name'] = $this->discipline->titre ?? null;
        $array['instructor_name'] = $this->instructor->name ?? null;


        return $array;
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}
