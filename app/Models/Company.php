<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function users()
{
    return $this->hasMany(User::class);
}

}
