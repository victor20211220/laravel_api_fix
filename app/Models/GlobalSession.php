<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSession extends Model
{
    use HasFactory;
    protected $table='global_sessions';
    protected $primaryKey = 'sessionId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'sessionKey',
        'userKey',
        'sessionIp',
        'sessionOs',
        'sessionBrowser',
        'sessionCountry',
        'sessionClick',
    ];
}
