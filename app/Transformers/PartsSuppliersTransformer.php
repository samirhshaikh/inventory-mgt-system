<?php
namespace App\Transformers;

use App\Models\PartsSupplier;
use League\Fractal\TransformerAbstract;

class PartsSuppliersTransformer extends TransformerAbstract
{
    public function transform(PartsSupplier $model)
    {
        return [
            "id" => $model->id,
            "SupplierName" => ucwords($model->SupplierName),
            "ContactNo1" => $model->ContactNo1,
            "ContactNo2" => $model->ContactNo2,
            "ContactNo3" => $model->ContactNo3,
            "Address" => $model->Address,
            "City" => $model->City,
            "Balance" => $model->Balance ?: "",
            "Comments" => $model->Comments,
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
