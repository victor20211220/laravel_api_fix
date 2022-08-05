<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalClient extends Model
{
    use HasFactory;
    protected $table='global_clients';
    protected $primaryKey = 'clientId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'clientKey',
        'clientName',
        'clientEmail',
        'clientPassword',
        'clientStatus',
        'clientBillingAddress',
    ];
}
