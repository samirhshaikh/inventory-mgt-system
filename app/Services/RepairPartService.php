<?php

namespace App\Services;

use App\Models\RepairPart;

class RepairPartService
{
    /**
     * @param $repairId
     * @param array $parts
     * @return int
     */
    public function save($repairId, array $parts): int
    {
        $records_count = 0;
        foreach ($parts as $row) {
            //New Record
            if (empty($row["id"] ?? 0)) {
                $record = new RepairPart();

                $record->RepairId = $repairId;
                $record->CreatedBy = session("user_details.UserName");
            }
            //Edit Record
            else {
                $record = RepairPart::where("id", $row["id"])->get();

                if (!$record->count()) {
                    continue;
                }

                $record = $record->first();
            }
            $record->SupplierId = $row["supplier"]["id"];
            $record->PartId = $row["part"]["id"];
            $record->Cost = $row["Cost"];
            $record->UpdatedBy = session("user_details.UserName");
            $record->save();

            $records_count++;
        }

        return $records_count;
    }
}
