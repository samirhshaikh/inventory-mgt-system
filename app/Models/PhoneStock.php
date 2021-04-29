<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\PhoneStockTransformer;

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

    public function manufacturer() {
        return $this->hasOne(HandsetManufacturers::class, 'Id', 'MakeId');
    }

    public function model() {
        return $this->hasOne(HandsetModels::class, 'Id', 'ModelId');
    }

    public function color() {
        return $this->hasOne(HandsetColors::class, 'Id', 'ColorId');
    }

    public function sales() {
        return $this->hasOne(Sales::class, 'IMEI', 'IMEI');
    }

    public function stock_log() {
        return $this->hasMany(StockLog::class, 'IMEI', 'IMEI');
    }
}
