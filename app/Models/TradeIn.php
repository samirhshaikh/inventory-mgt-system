<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\TradeInTransformer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeIn extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "TradeIn";
    protected $primaryKey = ["SalesInvoiceId", "PurchaseInvoiceId"];
    protected $transformer = TradeInTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    /**
     * @return BelongsTo
     */
    public function sales(): BelongsTo
    {
        return $this->belongsTo(SalesStock::class, "Id", "SalesInvoiceId");
    }

    /**
     * @return BelongsTo
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(
            Purchase::class,
            "PurchaseInvoiceId",
            "Id"
        )->with("purchases");
    }
}
