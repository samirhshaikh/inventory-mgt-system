<?php

namespace App\Transformers;

use App\Models\Customers;
use League\Fractal\TransformerAbstract;

class CustomersTransformer extends TransformerAbstract
{
    public function transform(Customers $model)
    {
        return [
            "Id" => $model->Id,
            "CustomerName" => ucwords($model->CustomerName),
            "ContactNo1" => $model->ContactNo1,
            "ContactNo2" => $model->ContactNo2,
            "ContactNo3" => $model->ContactNo3,
            "Address" => $model->Address,
            "City" => $model->City,
            "Comments" => $model->Comments,
            "Balance" => $model->Balance ?: "",
            "IsActive" => boolval($model->IsActive),
            "CreatedDate" => $model->CreatedDate->format("d-M-Y h:i A"),
            "CreatedBy" => $model->CreatedBy,
            "UpdatedDate" => $model->UpdatedDate->format("d-M-Y h:i A"),
            "UpdatedBy" => empty($model->UpdatedBy)
                ? $model->CreatedBy
                : $model->UpdatedBy,
        ];
    }
}
