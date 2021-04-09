<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\PurchaseTransformer;

class Purchase extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'Purchase';
    protected $primaryKey = ['Id'];
    protected $transformer = PurchaseTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';

    protected $dates = [
        'InvoiceDate'
    ];

    public function supplier() {
        return $this->hasOne(Suppliers::class, 'Id', 'SupplierId');
    }

    public function purchases() {
        return $this->hasMany(PhoneStock::class, 'InvoiceId', 'Id')
            ->with('manufacturer')
            ->with('model')
            ->with('color')
            ;
    }
}
