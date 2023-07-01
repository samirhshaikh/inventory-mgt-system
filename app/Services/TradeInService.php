<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Models\TradeIn;

class TradeInService
{
    /**
     * @param string $purchaseInvoiceId
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingleTradeIn(string $purchaseInvoiceId)
    {
        $record = TradeIn::where("PurchaseInvoiceId", $purchaseInvoiceId)
            ->with("purchase")
            ->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param string $salesInvoiceId
     * @param string $purchaseInvoiceId
     */
    public function save(string $salesInvoiceId, string $purchaseInvoiceId)
    {
        try {
            $this->getSingleTradeIn($purchaseInvoiceId);
        } catch (RecordNotFoundException $e) {
            $record = new TradeIn();
            $record->SalesInvoiceId = $salesInvoiceId;
            $record->PurchaseInvoiceId = $purchaseInvoiceId;
            $record->save();
        }
    }

    /**
     * @param string $purchaseInvoiceId
     * @return bool
     * @throws RecordNotFoundException
     */
    public function delete(string $purchaseInvoiceId)
    {
        try {
            $this->getSingleTradeIn($purchaseInvoiceId);

            TradeIn::where("PurchaseInvoiceId", $purchaseInvoiceId)->delete();

            return true;
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException();
        }
    }
}
