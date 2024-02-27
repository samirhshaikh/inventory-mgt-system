<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\SaleTransformer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "Sales";
    protected $primaryKey = ["id"];
    protected $transformer = SaleTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    protected $dates = ["InvoiceDate"];

    /**
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, "id", "CustomerId");
    }

    /**
     * @return HasMany
     */
    public function sales(): HasMany
    {
        return $this->hasMany(SalesStock::class, "InvoiceId", "id")->with(
            "phone"
        );
    }

    /**
     * @return HasOne
     */
    public function tradein(): HasOne
    {
        return $this->hasOne(TradeIn::class, "SalesInvoiceId", "id")->with(
            "purchase"
        );
    }
}
