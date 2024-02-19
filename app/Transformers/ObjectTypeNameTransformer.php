<?php

namespace App\Transformers;

use App\Models\ObjectTypeName;
use League\Fractal\TransformerAbstract;

class ObjectTypeNameTransformer extends TransformerAbstract
{
    public function transform(ObjectTypeName $model)
    {
        return [
            "Id" => $model->Id,
            "Name" => ucwords($model->Name),
            "IsActive" => boolval($model->IsActive),
            "CreatedDate" => empty($model->CreatedDate)
                ? ""
                : $model->CreatedDate->format("d-M-Y h:i A"),
            "CreatedBy" => $model->CreatedBy,
            "UpdatedDate" => is_null($model->UpdatedDate)
                ? (empty($model->CreatedDate)
                    ? ""
                    : $model->CreatedDate->format("d-M-Y h:i A"))
                : $model->UpdatedDate->format("d-M-Y h:i A"),
            "UpdatedBy" => empty($model->UpdatedBy)
                ? $model->CreatedBy
                : $model->UpdatedBy,
        ];
    }
}
