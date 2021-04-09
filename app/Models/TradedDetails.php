<?php


namespace App\Models;

use App\Traits\CompositeKeysTrait;

class TradedDetails extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'TradedDetails';
//    protected $primaryKey = ['InvoiceId', 'PhoneStockId'];
//    protected $transformer = PurchaseTransformer::class;
//    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';
}
