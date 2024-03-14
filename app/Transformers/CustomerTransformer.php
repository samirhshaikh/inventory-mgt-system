<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    public function transform(Customer $model)
    {
        return [
            "id" => $model->id,
            "CustomerName" => ucwords($model->CustomerName),
            "ContactNo1" => $model->ContactNo1,
            "ContactNo2" => $model->ContactNo2,
            "Address" => $model->Address,
            "City" => $model->City,
            "Comments" => $model->Comments,
            "Balance" => $model->Balance ?: "",
            "IsActive" => boolval($model->IsActive),
            "CreatedDate" => is_null($model->CreatedDate)
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
