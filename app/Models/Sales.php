<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\SalesTransformer;

class Sales extends BaseModel {
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'Sales';
    protected $primaryKey = ['Id'];
    protected $transformer = SalesTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';

    protected $dates = [
        'InvoiceDate'
    ];

    public function customer() {
        return $this->hasOne(Customers::class, 'Id', 'CustomerId');
    }

    public function sales() {
        return $this->hasMany(SalesStock::class, 'InvoiceId', 'Id')
            ->with('phone');
    }
}
