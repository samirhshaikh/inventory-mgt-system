<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\RepairPartTransformer;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RepairPart extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "Repairs_Parts";
    protected $primaryKey = ["id"];
    protected $transformer = RepairPartTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    /**
     * @return HasOne
     */
    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, "id", "SupplierId");
    }

    /**
     * @return HasOne
     */
    public function part(): HasOne
    {
        return $this->hasOne(Part::class, "id", "PartId");
    }
}
