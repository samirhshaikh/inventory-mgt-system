<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Models\PhoneStock;
use App\Models\SalesStock;
use App\Models\StockLog;
use App\Traits\TableActions;

class SalesStockService
{
    use TableActions;

    /**
     * @param $invoiceId
     * @param array $phones
     * @return int
     */
    public function save($invoiceId, array $phones): int
    {
        $phonestock_service = new PhoneStockService();

        $records_count = 0;
        foreach ($phones as $row) {
            try {
                //Change the status in phonestock
                $phonestock_service->changePhoneAvailabilityStatus(
                    $row["IMEI"],
                    PhoneStock::STATUS_SOLD
                );

                //New Record
                if (empty($row["id"] ?? 0)) {
                    $stocklog_service = new StockLogService();
                    $stocklog_service->add(
                        $row["IMEI"],
                        StockLog::ACTIVITY_SOLD
                    );

                    $record = new SalesStock();

                    $record->InvoiceId = $invoiceId;
                    $record->CreatedBy = session("user_details.UserName");
                }
                //Edit Record
                else {
                    $record = SalesStock::where("id", $row["id"])->get();

                    if (!$record->count()) {
                        continue;
                    }

                    $record = $record->first();
                }

                $record->IMEI = $row["IMEI"];
                $record->Qty = 1;
                $record->Cost = $row["Cost"];
                $record->Discount = $row["Discount"] ?? 0;
                $record->UpdatedBy = session("user_details.UserName");
                $record->save();

                $records_count++;
            } catch (RecordNotFoundException $e) {
            }
        }

        return $records_count;
    }
}
