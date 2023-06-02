<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'url',
        'description',
        'niveau',
        'price',
        'instructor_id',
        'discipline_id',
        'classe_id',
        'background-image',
        'views_number',
        'sells_number',
        'duration',
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
        $array['classe_name'] = $this->classe->titre ?? null;
        $array['instructor_name'] = $this->instructor->name ?? null;


        return $array;
    }
    public function packs()
    {
        return $this->belongsToMany(Pack::class);
    }
}
