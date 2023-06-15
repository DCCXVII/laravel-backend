<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subscribtion;

class subscriber extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscribtion()
    {
        return $this->belongsTo(Subscribtion::class);
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['client_name'] = $this->user->name ?? null;
        $array['client_email'] = $this->user->email ?? null;
        $array['subscription'] = $this->subscribtion->title ?? null;
        $array['duration'] = $this->subscribtion->duration ?? null;



        return $array;
    }
}
