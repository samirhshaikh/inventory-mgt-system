<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\PurchaseTransformer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "Purchase";
    protected $primaryKey = ["Id"];
    protected $transformer = PurchaseTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    protected $dates = ["InvoiceDate"];

    /**
     * @return HasOne
     */
    public function supplier(): HasOne
    {
        return $this->hasOne(Suppliers::class, "Id", "SupplierId");
    }

    /**
     * @return HasMany
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(PhoneStock::class, "InvoiceId", "Id")
            ->with("manufacturer")
            ->with("model")
            ->with("color");
    }
}
