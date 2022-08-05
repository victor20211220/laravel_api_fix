<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalImpression extends Model
{
    use HasFactory;
    protected $table='global_impressions';
    protected $primaryKey = 'impressionsId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'impressionsKey',
        'advertKey',
        'campaignKey',
        'sessionKey',
        'impressionsUrl',
        'impressionsBackfill',
        'backfillKey',
    ];
}
