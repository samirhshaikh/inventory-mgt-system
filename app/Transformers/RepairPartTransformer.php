<?php

namespace App\Transformers;

use App\Models\RepairPart;
use League\Fractal\TransformerAbstract;

class RepairPartTransformer extends TransformerAbstract
{
    public function transform(RepairPart $model)
    {
        $return = [
            "id" => $model->id,
            "Cost" => $model->Cost ?: "",
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

        if ($model->relationLoaded("supplier")) {
            if ($model->supplier) {
                $return["supplier"] = $model->supplier->transform();
            }
        }

        if ($model->relationLoaded("part")) {
            if ($model->part) {
                $return["part"] = $model->part->transform();
            }
        }

        return $return;
    }
}
