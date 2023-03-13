<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable;
    protected $guard = 'user';

    protected $fillable = [
        'region_id',
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password'
    ];
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);

    }
    public function  role(): BelongsTo
    {
        return  $this->belongsTo(Role::class);
    }

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();

    }

    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];

    }
}
