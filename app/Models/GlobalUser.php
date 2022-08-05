<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalUser extends Model
{
    use HasFactory;
    protected $table='global_users';
    protected $primaryKey = 'userId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'userKey',
        'userIp',
        'userDeviceType',
        'userOs',
        'userInboundUrl',
        'userLandingUrl',
        'userCountry'
    ];
}
