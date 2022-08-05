<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalPublisher extends Model
{
    use HasFactory;
    protected $table='global_publishers';
    protected $primaryKey = 'publisherId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'publisherKey',
        'publisherUrl',
        'publisherStatus',
    ];
}
