<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\GenerateUUID;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, GenerateUUID;

    // User gender enum constants
    const GENDER = [
        'Female'    => 'Female',
        'Male'      => 'Male',
        'Others'    => 'Others',
    ];

    // User status enum constants
    const STATUS = [
        'Active'    => 'Active',
        'Banned'    => 'Banned',
        'Blocked'   => 'Blocked',
        'Inactive'  => 'Inactive',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

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
        'password' => 'hashed',
    ];

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('uuid', $value)->firstOrFail();
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): string
    {
        return !empty($this->first_name) ? \Illuminate\Support\Str::title($this->first_name . ' ' . $this->last_name) : 'Unavailable';
    }
}
