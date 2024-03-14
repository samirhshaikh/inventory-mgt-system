<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\CustomerTransformer;

class Customer extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "customers";
    protected $primaryKey = ["id"];
    protected $transformer = CustomerTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";
}
