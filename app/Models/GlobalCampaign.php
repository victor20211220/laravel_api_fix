<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalCampaign extends Model
{
    use HasFactory;
    protected $table='global_campaigns';
    protected $primaryKey = 'campaignId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'campaignKey',
        'campaignName',
        'clientKey',
        'campaignStart',
        'campaignEnd',
        'campaignImpressionLimit',
        'campaignRestrictionDeviceType',
        'campaignRestrictionTargetGeographyType',
        'campaignRestrictionTargetGeographyValue',
        'campaignRestrictionExcludedGeographyType',
        'campaignRestrictionExcludedGeographyValue',
    ];
}
