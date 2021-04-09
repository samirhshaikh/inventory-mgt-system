<?php
namespace App\Models;

use App\Models\BaseModel;
use App\Traits\CompositeKeysTrait;
use App\Transformer\UserTransformer;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends BaseModel implements Authenticatable, JWTSubject {
    use AuthenticatableTrait;
    use CompositeKeysTrait;

    protected $connection = 'mysql';
    protected $table = 'User';
    protected $primaryKey = ['UserName'];
    protected $transformer = UserTransformer::class;
    public $incrementing = false;
    public $timespamps = true;
    protected $guarded = [];

    const CREATED_AT = 'CreatedDate';
    const UPDATED_AT = 'UpdatedDate';

    public function getAuthIdentifierName() {
        return $this->getAttribute('UserName');
    }

    public function getAuthPassword() {
        return $this->getAttribute('Password');
    }

    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifierName();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
