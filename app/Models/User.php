<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
        'full_risk_access',     // <─ tambahkan ini
        'can_create_risk',      // <─ dan ini
        'can_update_risk',      // <─ dan ini
        'can_delete_risk',      // <─ dan ini

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true; 
    }

    public function canAccessFilament(): bool
{
    return true;
}
public function removeRoleIfMultiple($roleName)
{
    if ($this->roles()->count() > 1) {
        $this->removeRole($roleName); // hapus role tertentu
        return true;
    }
    return false; // tidak dihapus karena hanya 1 role
}

public function company()
{
    return $this->belongsTo(Company::class);
}

public function divisions()
{
    // Relasi User → Division melalui pivot division_user
    return $this->belongsToMany(Division::class, 'division_user')
        ->withPivot('division_year_id') // hanya kolom pivot yang ada
        ->withTimestamps();
}

public function divisionYears()
{
    // Relasi User → DivisionYear melalui pivot division_user
    return $this->belongsToMany(DivisionYear::class, 'division_user', 'user_id', 'division_year_id')
        ->withPivot('division_id') // kolom pivot yang ada
        ->withTimestamps();
}

}
