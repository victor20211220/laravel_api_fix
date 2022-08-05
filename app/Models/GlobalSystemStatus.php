<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSystemStatus extends Model
{
    use HasFactory;
    protected $table='global_systemstatus';
    protected $primaryKey = 'global_systemStatusId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'global_systemStatusValue'
    ];
}
