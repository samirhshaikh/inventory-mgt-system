<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\PhoneStockTransformer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PhoneStock extends BaseModel {
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'PhoneStock';
    protected $primaryKey = ['Id'];
    protected $transformer = PhoneStockTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';

    const STATUS_IN_STOCK = 'In Stock';
    const STATUS_SOLD = 'Sold';

    /**
     * @return HasOne
     */
    public function manufacturer(): HasOne {
        return $this->hasOne(HandsetManufacturers::class, 'Id', 'MakeId');
    }

    /**
     * @return HasOne
     */
    public function model(): HasOne {
        return $this->hasOne(HandsetModels::class, 'Id', 'ModelId');
    }

    /**
     * @return HasOne
     */
    public function color(): HasOne {
        return $this->hasOne(HandsetColors::class, 'Id', 'ColorId');
    }

    /**
     * @return HasOne
     */
    public function sales(): HasOne {
        return $this->hasOne(Sales::class, 'Id', 'InvoiceId');
    }

    /**
     * @return HasMany
     */
    public function stock_log(): HasMany {
        return $this->hasMany(StockLog::class, 'IMEI', 'IMEI');
    }

    /**
     * @return BelongsTo
     */
    public function purchase(): BelongsTo {
        return $this->belongsTo(Purchase::class, 'InvoiceId', 'Id');
    }
}
