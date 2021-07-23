<?php

namespace App\Transformers;

use App\Models\SalesStock;
use League\Fractal\TransformerAbstract;

class SalesStockTransformer extends TransformerAbstract
{
    public function transform(SalesStock $model)
    {
        $return = [
            'Id' => $model->Id,
            'InvoiceId' => $model->InvoiceId,
            'IMEI' => $model->IMEI,
            'Qty' => $model->Qty,
            'Description' => $model->Description,
            'Cost' => $model->Cost ? number_format($model->Cost, 2) : '',
            'Discount' => $model->Discount ? number_format($model->Discount, 2) : '',
            'Returned' => $model->Returned,
            'ReturnedDate' => empty($model->ReturnedDate) ? '' : $model->ReturnedDate->format('d-M-Y'),
            'CreatedDate' => empty($model->CreatedDate) ? '' : $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => is_null($model->UpdatedDate) ? (empty($model->CreatedDate) ? '' : $model->CreatedDate->format('d-M-Y h:i A')) : $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];

        if ($model->relationLoaded('phone')) {
            if ($model->phone) {
                $return['phone_details'] = $model->phone->transform();
            }
        }

        return $return;
    }
}
