<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\SupplierTransformer;

class Supplier extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "Supplier";
    protected $primaryKey = ["id"];
    protected $transformer = SupplierTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";
}
