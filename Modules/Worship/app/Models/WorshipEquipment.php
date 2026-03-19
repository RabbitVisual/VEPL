<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Worship\Database\Factories\WorshipEquipmentFactory;

class WorshipEquipment extends Model
{
    use HasFactory;

    protected $table = 'worship_equipments';

    protected $fillable = [
        'name',
        'worship_team_role_id',
        'status',
        'serial_number',
        'notes',
    ];

    public function role()
    {
        return $this->belongsTo(WorshipTeamRole::class, 'worship_team_role_id');
    }
}
