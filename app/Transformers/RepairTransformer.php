<?php

namespace App\Transformers;

use App\Models\Repair;
use League\Fractal\TransformerAbstract;

class RepairTransformer extends TransformerAbstract
{
    public function transform(Repair $model)
    {
        $return = [
            "id" => $model->id,
            "InvoiceNo" => $this->formatInvoiceNumber(
                $model->InvoiceNo,
                $model->InvoiceDate
            ),
            "ReceivedDate" => empty($model->ReceivedDate)
                ? ""
                : $model->ReceivedDate->format("d-M-Y"),
            "InvoiceDate" => empty($model->InvoiceDate)
                ? ""
                : $model->InvoiceDate->format("d-M-Y"),
            "CustomerId" => $model->CustomerId,
            "MakeId" => $model->MakeId,
            "ModelId" => $model->ModelId,
            "ColorId" => $model->ColorId,
            "PaymentMethod" => $model->PaymentMethod,
            "ChequeNo" => $model->ChequeNo,
            "Status" => $model->Status,
            "Notes" => $model->Notes,
            "ReasonForNotRepair" => $model->ReasonForNotRepair,
            "Amount" => $model->Amount ?: "",
            "VAT" => $model->VAT ?: "",
            "IMEI" => $model->IMEI ?: "",
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

        $return["phone_details"] = [
            "manufacturer" => [],
            "model" => [],
            "color" => [],
        ];

        if ($model->relationLoaded("manufacturer")) {
            if ($model->manufacturer) {
                $return["phone_details"][
                    "manufacturer"
                ] = $model->manufacturer->transform();
            }
        }

        if ($model->relationLoaded("model")) {
            if ($model->model) {
                $return["phone_details"]["model"] = $model->model->transform();
            }
        }

        if ($model->relationLoaded("color")) {
            if ($model->color) {
                $return["phone_details"]["color"] = $model->color->transform();
            }
        }

        $return["customer"] = [];
        if ($model->relationLoaded("customer")) {
            if ($model->customer) {
                $return["customer"] = $model->customer->transform();
            }
        }

        $return["children"] = [];
        if ($model->relationLoaded("parts")) {
            if ($model->parts) {
                foreach ($model->parts as $key => $value) {
                    $child = $value->transform();
                    $child["_level"] = 1;

                    $return["children"][] = $child;
                }
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
            "REP",
            $invoiceDate->format("Y"),
            $invoiceDate->format("m"),
            $invoiceDate->format("d"),
            $invoiceNo
        );
    }
}
