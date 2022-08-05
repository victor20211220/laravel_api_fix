<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAvert extends Model
{
    use HasFactory;
    protected $table='global_adverts';
    protected $primaryKey = 'advertId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'advertKey',
        'clientKey',
        'publisherKey',
        'campaignKey',
        'advertImagePath',
        'advertPosition',
        'advertUrl',
        'advertStatus',
        'advertImpressionLimit',
        'advertImpressionCount',
    ];
}
