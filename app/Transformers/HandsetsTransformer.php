<?php

namespace App\Transformers;

use App\Models\Handsets;
use League\Fractal\TransformerAbstract;

class HandsetsTransfomer extends TransformerAbstract {
    public function transform(Handsets $model) {
        $return = [
            'Id' => $model->Id,
            'Name' => $model->Name,
            'MakeId' => (int)$model->MakeId,
            'manufacturer' => $model->manufacturer,
            'ModelId' => (int)$model->ModelId,
            'model' => $model->model,
            'ColorId' => (int)$model->ColorId,
            'color' => $model->color,
            'IsActive' => boolval($model->IsActive),
            'CreatedDate' => date('d-M-Y h:i A', strtotime($model->CreatedDate)),
            'CreatedBy' => $model->CreatedBy,
            'UpdatedDate' => empty($model->UpdatedBy) ? date('d-M-Y h:i A', strtotime($model->CreatedDate)) : date('d-M-Y h:i A', strtotime($model->UpdatedDate)),
            'UpdatedBy' => empty($model->UpdatedBy) ? $model->CreatedBy : $model->UpdatedBy
        ];

        return $return;
    }
}
