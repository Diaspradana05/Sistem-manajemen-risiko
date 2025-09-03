<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DivisionUser extends Pivot
{
    public function divisionYear()
{
    return $this->belongsTo(DivisionYear::class, 'division_year_id');
}

}
