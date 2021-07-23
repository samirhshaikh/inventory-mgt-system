<?php

namespace App\Transformers;

use App\Models\PhoneStock;
use League\Fractal\TransformerAbstract;

class PhoneStockTransformer extends TransformerAbstract
{
    public function transform(PhoneStock $model)
    {
        $return = [
            'Id' => $model->Id,
            'IMEI' => $model->IMEI,
            'Size' => $model->Size,
            'Cost' => $model->Cost ? number_format($model->Cost, 2) : '',
            'StockType' => ucwords(strtolower($model->StockType)),
            'ModelNo' => $model->ModelNo,
            'Network' => $model->Network,
            'Status' => ucwords(strtolower($model->Status)),
            'manufacturer' => $model->manufacturer,
            'model' => $model->model,
            'color' => $model->color,
            'IsActive' => boolval($model->IsActive),
            'CreatedDate' => empty($model->CreatedDate) ? '' : $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => is_null($model->UpdatedDate) ? (empty($model->CreatedDate) ? '' : $model->CreatedDate->format('d-M-Y h:i A')) : $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];

        $return['phone_details'] = [
            'manufacturer' => [],
            'model' => [],
            'color' => []
        ];

        if ($model->relationLoaded('manufacturer')) {
            if ($model->manufacturer) {
                $return['phone_details']['manufacturer'] = $model->manufacturer->transform();
            }
        }

        if ($model->relationLoaded('model')) {
            if ($model->model) {
                $return['phone_details']['model'] = $model->model->transform();
            }
        }

        if ($model->relationLoaded('color')) {
            if ($model->color) {
                $return['phone_details']['color'] = $model->color->transform();
            }
        }

        if ($model->relationLoaded('sales')) {
            if ($model->sales) {
                $return['sales'] = $model->sales->transform();
            }
        }

        return $return;
    }
}
