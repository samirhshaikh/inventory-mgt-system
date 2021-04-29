<?php

namespace App\Transformers;

use App\Models\ObjectTypeName;
use League\Fractal\TransformerAbstract;

class ObjectTypeNameTransformer extends TransformerAbstract
{
    public function transform(ObjectTypeName $model)
    {
        return [
            'Id' => $model->Id,
            'Name' => $model->Name,
            'IsActive' => boolval($model->IsActive),
            'CreatedDate' => $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];
    }
}
