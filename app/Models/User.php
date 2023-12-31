<?php

namespace App\Models;



use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\SubscriptionExpiryNotification;
use Illuminate\Support\Facades\DB;



class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'img_url',
        'description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function packs()
    {
        return $this->hasMany(Pack::class, 'instructor_id');
    }

    public function verificationUrl($token)
    {
        // Customize the URL here
        return url("/verify-email/{$token}");
    }
    public function sendSubscriptionExpiryNotification()
    {
        $this->notify(new SubscriptionExpiryNotification());
    }

    public function hasPurchasedItem(int $itemId, string $itemType): bool
    {
        $query = DB::table('purchase_item')
            ->join('purchases', 'purchases.id', '=', 'purchase_item.purchase_id')
            ->where('purchases.client_id', $this->id)
            ->where('purchase_item.item_id', $itemId)
            ->where('purchase_item.item_type', $itemType)
            ->count();

        return $query > 0;
    }

    public function subscriber()
    {
        return $this->hasOne(Subscriber::class);
    }
}
