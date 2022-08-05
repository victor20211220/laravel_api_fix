<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAccessLog extends Model
{
    use HasFactory;
    protected $table='global_accesslog';
    protected $primaryKey = 'accessLogId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'logKey',
        'accessLogIp',
        'accessLogBrowser',
        'accessLogDevice',
    ];
}
