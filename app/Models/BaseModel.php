<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseModel extends Model
{
    protected $transformer = null;

    public function transform()
    {
        if (empty($this->transformer) || !class_exists($this->transformer)) {
            return $this;
        }

        return (new $this->transformer)->transform($this);
    }

    protected static function lastInsertId() {
        return DB::getPDO()->lastInsertId();
    }
}
?>
