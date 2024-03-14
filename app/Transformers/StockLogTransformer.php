<?php

namespace App\Transformers;

use App\Models\StockLog;
use League\Fractal\TransformerAbstract;

class StockLogTransformer extends TransformerAbstract
{
    public function transform(StockLog $model)
    {
        $return = [
            "id" => $model->id,
            "LogDate" => empty($model->LogDate)
                ? ""
                : $model->LogDate->format("d-M-Y"),
            "IMEI" => $model->IMEI,
            "Comments" => $model->Comments,
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

        if ($model->relationLoaded("phone")) {
            if ($model->phone) {
                $return["phone"] = $model->phone->transform();
            }
        }

        return $return;
    }
}
