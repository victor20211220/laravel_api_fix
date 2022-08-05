<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextualImpression extends Model
{
    use HasFactory;
    protected $table='contextual_impressions';
    protected $primaryKey = 'impressionId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'impressionKey',
        'sessionKey',
        'userKey',
        'keywordKey',
        'advertKey',
    ];
}
