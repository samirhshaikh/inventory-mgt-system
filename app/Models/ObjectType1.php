<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\ObjectType1Transformer;

abstract class ObjectType1 extends BaseModel {
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = '';
    protected $primaryKey = ['Id'];
    protected $transformer = ObjectType1Transformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';
}
