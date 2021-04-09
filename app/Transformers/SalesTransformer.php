<?php

namespace App\Transformers;

use App\Models\Sales;
use League\Fractal\TransformerAbstract;

class SalesTransformer extends TransformerAbstract
{
    public function transform(Sales $model) {
        $return = [
            'Id' => $model->Id,
            'InvoiceNo' => $model->InvoiceNo,
            'InvoiceDate' => empty($model->InvoiceDate) ? '' : $model->InvoiceDate->format('d-M-Y'),
            'CustomerId' => $model->CustomerId,
            'customer' => $model->customer,
            'RepairId' => $model->RepairId,
            'PaymentMethod' => $model->PaymentMethod,
            'ChequeNo' => $model->ChequeNo,
            'Comments' => $model->Comments,
            'VAT' => $model->VAT ? number_format($model->VAT, 2) : '',
            'CreatedDate' => $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy,
            '_level' => 0
        ];

        $return['customer'] = [];
        if ($model->relationLoaded('customer')) {
            if ($model->customer) {
                $return['customer'] = $model->customer->transform();
            }
        }

        $return['childs'] = [];
        if ($model->relationLoaded('sales')) {
            if ($model->sales) {
                foreach ($model->sales as $key => $value) {
                    $child = $value->transform();
                    $child['_level'] = 1;

                    $return['childs'][] = $child;
                }
            }
        }

        return $return;
    }
}
