<?php

namespace App\Transformers;

use App\Models\CustomerSales;
use League\Fractal\TransformerAbstract;

class CustomerSalesTransformer extends TransformerAbstract
{
    public function transform(CustomerSales $model)
    {
        return [
            'Id' => $model->Id,
            'CustomerName' => $model->CustomerName,
            'ContactNo1' => $model->ContactNo1,
            'ContactNo2' => $model->ContactNo2,
            'Address' => $model->Address,
            'City' => $model->City,
            'Comments' => $model->Comments,
            'Balance' => $model->Balance ? number_format($model->Balance, 2) : '',
            'Comments' => $model->Comments,
            'IsActive' => boolval($model->IsActive),
            'CreatedDate' => $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];
    }
}
