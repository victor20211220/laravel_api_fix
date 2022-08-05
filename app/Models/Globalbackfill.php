<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Globalbackfill extends Model
{
    use HasFactory;
    protected $table='global_backfill';
    protected $primaryKey = 'backfillId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'backfillKey',
        'backfillCompany',
        'backfillDeviceTypes',
        'backfillTargetGeographyType',
        'backfillTargetGeographyValue',
        'backfillExcludedGeographyType',
        'backfillExcludedGeographyValue',
        'backfillStatus',
    ];
}
