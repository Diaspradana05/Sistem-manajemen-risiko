<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisionYear extends Model
{
    protected $table = 'division_year';

    protected $fillable = [
        'division_id',
        'year',
    ];

    public function division()
{
    return $this->belongsTo(Division::class);
}

    public function users()
    {
        return $this->belongsToMany(User::class, 'division_user', 'division_year_id', 'user_id')
            ->withPivot('division_id')
            ->withTimestamps();
    }
}