<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\ObjectTypeNameTransformer;

abstract class ObjectTypeName extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "";
    protected $primaryKey = ["Id"];
    protected $transformer = ObjectTypeNameTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";
}
