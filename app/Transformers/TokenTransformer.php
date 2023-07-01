<?php

namespace App\Transformer;

use App\Models\Token;
use League\Fractal\TransformerAbstract;

class TokenTransformer extends TransformerAbstract
{
    public function transform(Token $model)
    {
        return [
            "username" => $model->UserName,
            "token" => $model->Token,
            "created_on" => date("D-M-Y", $model->CreatedDate),
            "updated_on" => date("D-M-Y", $model->UpdatedDate),
            "expires_on" => date("D-M-Y", $model->ExpiresAt),
        ];
    }
}
