<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\SalesTransformer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sales extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "Sales";
    protected $primaryKey = ["Id"];
    protected $transformer = SalesTransformer::class;
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
        return $this->hasOne(CustomerSales::class, "Id", "CustomerId");
    }

    /**
     * @return HasMany
     */
    public function sales(): HasMany
    {
        return $this->hasMany(SalesStock::class, "InvoiceId", "Id")->with(
            "phone"
        );
    }

    /**
     * @return HasOne
     */
    public function tradein(): HasOne
    {
        return $this->hasOne(TradeIn::class, "SalesInvoiceId", "Id")->with(
            "purchase"
        );
    }
}
