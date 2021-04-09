<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\CustomersTransfomer;

class Customers extends BaseModel {
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'Customer';
    protected $primaryKey = ['Id'];
    protected $transformer = CustomersTransfomer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';
}
