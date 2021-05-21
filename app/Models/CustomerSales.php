<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\CustomerSalesTransformer;

class CustomerSales extends BaseModel {
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'Customer_Sales';
    protected $primaryKey = ['Id'];
    protected $transformer = CustomerSalesTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';
}
