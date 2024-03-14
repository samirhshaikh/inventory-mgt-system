<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\PartsSuppliersTransformer;

class PartsSupplier extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "Parts_Supplier";
    protected $primaryKey = ["id"];
    protected $transformer = PartsSuppliersTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";
}
