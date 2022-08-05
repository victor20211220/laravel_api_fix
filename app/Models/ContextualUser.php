<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextualUser extends Model
{
    use HasFactory;
    protected $table='contextual_users';
    protected $primaryKey = 'userId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'userKey',
        'userIp',
        'userDeviceType',
        'userOs',
        'userCountry',
    ];
}
