<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalClientInvoice extends Model
{
    use HasFactory;
    protected $table='global_clientinvoices';
    protected $primaryKey = 'clientInvoiceId';

    const CREATED_AT = 'dateTimeAdded';
    const UPDATED_AT = 'dateTimeModified';

    protected $fillable = [
        'clientInvoiceKey',
        'clientKey',
        'clientInvoiceAmount',
        'clientInvoiceDate',
        'clientInvoiceDescription',
        'clientInvoiceStatus',
    ];
}
