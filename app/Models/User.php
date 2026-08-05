<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'otp', 
        'is_verified',
        'is_active',
        'profile_picture'
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

    public function info(): HasOne{
        return $this->hasOne(UserInfo::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(ClientAppointment::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class)
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' || $this->role === 'staff';
    }

    public function purchaseAccounts()
    {   
        return $this->hasMany(PurchaseAccount::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(LotReservation::class);
    }

    public function commissionRequests()
    {
        return $this->hasMany(
            CommissionRequest::class,
            'agent_id'
        );
    }
}
