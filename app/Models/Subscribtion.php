<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribtion extends Model
{
    use HasFactory;
    public function subscriber()
    {
        return $this->hasOne(subscriber::class);
    }
}
