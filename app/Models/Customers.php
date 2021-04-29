<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\CustomersTransformer;

class Customers extends BaseModel {
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'Customer';
    protected $primaryKey = ['Id'];
    protected $transformer = CustomersTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';
}
