<?php

namespace App\Transformers;

use App\Models\Purchase;
use League\Fractal\TransformerAbstract;

class PurchaseTransformer extends TransformerAbstract
{
    public function transform(Purchase $model)
    {
        $return = [
            'Id' => $model->Id,
            'InvoiceNo' => $model->InvoiceNo,
            'InvoiceDate' => empty($model->InvoiceDate) ? '' : $model->InvoiceDate->format('d-M-Y'),
            'SupplierId' => (int)$model->SupplierId,
            'Comments' => $model->Comments,
            'CreatedDate' => $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy,
            '_level' => 0
        ];

        $return['supplier'] = [];
        if ($model->relationLoaded('supplier')) {
            if ($model->supplier) {
                $return['supplier'] = $model->supplier->transform();
            }
        }

        $return['children'] = [];
        if ($model->relationLoaded('purchases')) {
            if ($model->purchases) {
                foreach ($model->purchases as $key => $value) {
                    $child = $value->transform();
                    $child['_level'] = 1;

                    $return['children'][] = $child;
                }
            }
        }

        return $return;
    }
}
