<?php

namespace App\Transformers;
use App\Models\TradeIn;
use League\Fractal\TransformerAbstract;

class TradeInTransformer extends TransformerAbstract
{
    public function transform(TradeIn $model)
    {
        $return = [
            "SalesInvoiceId" => $model->SalesInvoiceId,
            "PurchaseInvoiceId" => $model->PurchaseInvoiceId,
        ];

        if ($model->relationLoaded("purchase")) {
            if ($model->purchase) {
                $return["purchase"] = $model->purchase->transform();
            }
        }

        return $return;
    }
}
