<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\RepairTransformer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Repair extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "Repairs";
    protected $primaryKey = ["id"];
    protected $transformer = RepairTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    protected $dates = ["InvoiceDate", "ReceivedDate"];

    /**
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, "id", "CustomerId");
    }

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
     * @return HasMany
     */
    public function parts(): HasMany
    {
        return $this->hasMany(RepairPart::class, "RepairId", "id")
            ->with("supplier")
            ->with("part");
    }
}
