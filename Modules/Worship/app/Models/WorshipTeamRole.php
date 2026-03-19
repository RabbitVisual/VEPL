<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Worship\Database\Factories\WorshipTeamRoleFactory;

class WorshipTeamRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description',
    ];
}
