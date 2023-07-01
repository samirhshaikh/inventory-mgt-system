<?php
namespace App\Models;

use App\Transformer\TokenTransformer;

class Token extends BaseModel
{
    protected $connection = "mysql";
    protected $table = "Token";
    protected $primaryKey = ["UserName"];
    protected $transformer = TokenTransformer::class;
    public $incrementing = false;
    public $timespamps = false;
    protected $guarded = [];
}
?>
