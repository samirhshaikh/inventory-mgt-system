<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $model)
    {
        return [
            'UserName' => $model->UserName,
            'Password' => '***',
            'IsAdmin' => boolval($model->IsAdmin),
            'IsActive' => boolval($model->IsActive),
            'CreatedDate' => empty($model->CreatedDate) ? '' : $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => is_null($model->UpdatedDate) ? (empty($model->CreatedDate) ? '' : $model->CreatedDate->format('d-M-Y h:i A')) : $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];
    }
}

?>
