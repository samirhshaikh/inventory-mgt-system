<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ObjectType1;

class ObjectType1Transformer extends TransformerAbstract {
    public function transform(ObjectType1 $model) {
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
