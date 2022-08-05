<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextualClick extends Model
{
    use HasFactory;
    protected $table='contextual_clicks';
    protected $primaryKey = 'clickId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'clickKey',
        'sessionKey',
        'userKey',
        'impressionKey',
        'keywordKey',
        'advertKey',
    ];
}
