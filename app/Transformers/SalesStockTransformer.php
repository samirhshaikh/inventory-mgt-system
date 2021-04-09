<?php

namespace App\Transformers;

use App\Models\SalesStock;
use League\Fractal\TransformerAbstract;

class SalesStockTransformer extends TransformerAbstract
{
    public function transform(SalesStock $model) {
        $return = [
            'Id' => $model->Id,
            'InvoiceId' => $model->InvoiceId,
            'IMEI' => $model->IMEI,
            'Cost' => $model->Cost ? number_format($model->Cost, 2) : '',
            'Discount' => $model->Discount ? number_format($model->Discount, 2) : '',
            'CreatedDate' => $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];

        if ($model->relationLoaded('phone')) {
            if ($model->phone) {
                $return['phone'] = $model->phone->transform();
            }
        }

        return $return;
    }
}
