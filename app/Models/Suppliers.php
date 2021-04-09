<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\SuppliersTransformer;

class Suppliers extends BaseModel {
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'Supplier';
    protected $primaryKey = ['Id'];
    protected $transformer = SuppliersTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';
}
