<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\StockLogTransformer;

class StockLog extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'stock_log';
    protected $primaryKey = ['Id'];
    protected $transformer = StockLogTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';

    const ACTIVITY_RETURNED = 'Returned';
    const ACTIVITY_SOLD = 'Sold';
    const ACTIVITY_DELETED = 'Deleted';

    protected $dates = [
        'LogDate'
    ];

    public function phone() {
        return $this->hasOne(PhoneStock::class, 'IMEI', 'IMEI');
    }
}
