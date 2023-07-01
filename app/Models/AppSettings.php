<?php

namespace App\Models;

use App\Traits\CompositeKeysTrait;

class AppSettings extends BaseModel
{
    use CompositeKeysTrait;

    protected $connection = "mysql";
    protected $table = "App_Settings";
    protected $primaryKey = ["UserName", "State"];
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";
}
