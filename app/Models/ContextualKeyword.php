<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextualKeyword extends Model
{
    use HasFactory;
    protected $table='contextual_keywords';
    protected $primaryKey = 'keywordId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'keywordKey',
        'keywordText',
        'keywordStatus'
    ];
}
