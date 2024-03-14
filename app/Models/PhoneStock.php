<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\PhoneStockTransformer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PhoneStock extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "PhoneStock";
    protected $primaryKey = ["id"];
    protected $transformer = PhoneStockTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    const STATUS_IN_STOCK = "In Stock";
    const STATUS_SOLD = "Sold";

    /**
     * @return HasOne
     */
    public function manufacturer(): HasOne
    {
        return $this->hasOne(HandsetManufacturers::class, "id", "MakeId");
    }

    /**
     * @return HasOne
     */
    public function model(): HasOne
    {
        return $this->hasOne(HandsetModels::class, "id", "ModelId");
    }

    /**
     * @return HasOne
     */
    public function color(): HasOne
    {
        return $this->hasOne(HandsetColors::class, "id", "ColorId");
    }

    /**
     * @return HasOne
     */
    public function sales(): HasOne
    {
        return $this->hasOne(Sale::class, "id", "InvoiceId");
    }

    /**
     * @return HasMany
     */
    public function stock_log(): HasMany
    {
        return $this->hasMany(StockLog::class, "IMEI", "IMEI");
    }

    /**
     * @return BelongsTo
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, "InvoiceId", "id");
    }
}
