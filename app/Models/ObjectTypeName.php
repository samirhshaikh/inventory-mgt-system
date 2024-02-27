<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\ObjectTypeNameTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class ObjectTypeName extends BaseModel
{
    use HasFactory, CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "";
    protected $primaryKey = ["id"];
    protected $transformer = ObjectTypeNameTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";
}
