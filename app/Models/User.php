<?php
namespace App\Models;

use App\Traits\CompositeKeysTrait;
use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $connection = "mysql";
    protected $table = "User";
    protected $primaryKey = "UserName";
    protected $transformer = UserTransformer::class;
    public $incrementing = false;
    public $timestamps = true;
    protected $guarded = [];

    const CREATED_AT = "CreatedDate";
    const UPDATED_AT = "UpdatedDate";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["username", "password"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "email_verified_at" => "datetime",
    ];

    public function transform()
    {
        if (empty($this->transformer) || !class_exists($this->transformer)) {
            return $this;
        }

        return (new $this->transformer())->transform($this);
    }

    protected static function lastInsertId()
    {
        return DB::getPDO()->lastInsertId();
    }

    public function getAuthIdentifierName()
    {
        return $this->getAttribute("UserName");
    }

    public function getAuthPassword()
    {
        return $this->getAttribute("Password");
    }

    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifierName();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
