<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\HandsetsTransfomer;

class Handsets extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "HandsetMaster";
    protected $primaryKey = ["Id"];
    protected $transformer = HandsetsTransfomer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";
}
