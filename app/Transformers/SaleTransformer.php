<?php

namespace App\Transformers;

use App\Models\Sale;
use League\Fractal\TransformerAbstract;

class SaleTransformer extends TransformerAbstract
{
    public function transform(Sale $model)
    {
        $return = [
            "id" => $model->id,
            "InvoiceNo" => is_numeric($model->InvoiceNo)
                ? $this->formatInvoiceNumber(
                    $model->InvoiceNo,
                    $model->InvoiceDate
                ) //New Invoice No
                : $model->InvoiceNo, //Old Invoice No
            "InvoiceDate" => empty($model->InvoiceDate)
                ? ""
                : $model->InvoiceDate->format("d-M-Y"),
            "CustomerId" => $model->CustomerId,
            "customer" => $model->customer,
            "PaymentMethod" => $model->PaymentMethod,
            "ChequeNo" => $model->ChequeNo,
            "Comments" => $model->Comments,
            "VAT" => $model->VAT ? number_format($model->VAT, 2) : "",
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
            "_level" => 0,
        ];

        $return["customer"] = [];
        if ($model->relationLoaded("customer")) {
            if ($model->customer) {
                $return["customer"] = $model->customer->transform();
            }
        }

        $return["children"] = [];
        if ($model->relationLoaded("sales")) {
            if ($model->sales) {
                foreach ($model->sales as $key => $value) {
                    $child = $value->transform();
                    $child["_level"] = 1;

                    $return["children"][] = $child;
                }
            }
        }

        if ($model->relationLoaded("tradein")) {
            if ($model->tradein) {
                $return["tradein"] = $model->tradein->transform();
            }
        }

        return $return;
    }

    /**
     * @param $invoiceNo
     * @param $invoiceDate
     * @return string
     */
    private function formatInvoiceNumber($invoiceNo, $invoiceDate): string
    {
        return sprintf(
            "%s-%s%s%s000%s",
            "INV",
            $invoiceDate->format("Y"),
            $invoiceDate->format("m"),
            $invoiceDate->format("d"),
            $invoiceNo
        );
    }
}
