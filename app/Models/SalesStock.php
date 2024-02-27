<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\SalesStockTransformer;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SalesStock extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "SalesStock";
    protected $primaryKey = ["id"];
    protected $transformer = SalesStockTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    protected $dates = ["ReturnedDate"];

    /**
     * @return HasOne
     */
    public function phone(): HasOne
    {
        return $this->hasOne(PhoneStock::class, "IMEI", "IMEI")
            ->with("manufacturer")
            ->with("model")
            ->with("color");
    }
}
