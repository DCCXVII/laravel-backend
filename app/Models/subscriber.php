<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\subscription;

class subscriber extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(subscription::class);
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['client_name'] = $this->user->name ?? null;
        $array['client_email'] = $this->user->email ?? null;
        $array['subscription'] = $this->subscription->title ?? null;
        $array['duration'] = $this->subscription->duration ?? null;



        return $array;
    }
}
