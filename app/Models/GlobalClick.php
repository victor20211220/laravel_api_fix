<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalClick extends Model
{
    use HasFactory;
    protected $table='global_clicks';
    protected $primaryKey = 'clickId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'clickKey',
        'sessionKey',
        'advertKey',
        'impressionKey',
    ];
}
