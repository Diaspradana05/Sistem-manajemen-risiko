<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
{
    return $this->belongsToMany(User::class, 'division_user')
        ->withPivot('division_year_id')
        ->withTimestamps();
}

public function years()
{
    return $this->hasMany(DivisionYear::class);
}

}
