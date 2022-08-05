<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextualAdvert extends Model
{
    use HasFactory;
    protected $table='contextual_adverts';
    protected $primaryKey = 'advertId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'advertKey',
        'advertPosition',
        'advertImagePath',
        'advertStatus',
        'advertUrl',
        'keywordKey',
    ];
}
