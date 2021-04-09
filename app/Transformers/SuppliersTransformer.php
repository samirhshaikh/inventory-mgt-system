<?php

namespace App\Transformers;

use App\Models\Suppliers;
use League\Fractal\TransformerAbstract;

class SuppliersTransformer extends TransformerAbstract {
    public function transform(Suppliers $model) {
        return [
            'Id' => $model->Id,
            'SupplierName' => $model->SupplierName,
            'ContactNo1' => $model->ContactNo1,
            'ContactNo2' => $model->ContactNo2,
            'ContactNo3' => $model->ContactNo3,
            'Address' => $model->Address,
            'City' => $model->City,
            'CurrentBalance' => $model->CurrentBalance ? number_format($model->CurrentBalance, 2) : '',
            'Comments' => $model->Comments,
            'IsActive' => boolval($model->IsActive),
            'CreatedDate' => $model->CreatedDate->format('d-M-Y h:i A'),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => $model->UpdatedDate->format('d-M-Y h:i A'),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];
    }
}
