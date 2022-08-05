<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAdmin extends Model
{
    use HasFactory;
    protected $table='global_admins';
    protected $primaryKey = 'adminId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'adminKey',
        'adminName',
        'adminEmail',
        'adminPassword',
        'adminStatus',
    ];
}
