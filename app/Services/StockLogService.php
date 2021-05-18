<?php

namespace App\Services;

use App\Models\StockLog;
use Carbon\Carbon;

class StockLogService
{
    /**
     * @param $imei
     * @param string $activity
     * @param string $comments
     * @return bool
     */
    public function add($imei, string $activity, string $comments = '')
    {
        $record = new StockLog();
        $record->IMEI = $imei;
        $record->Activity = $activity;
        $record->LogDate = Carbon::now()->toDateTimeString();
        $record->Comments = $comments;
        $record->CreatedBy = session('user_details.UserName');
        $record->UpdatedBy = session('user_details.UserName');
        $record->save();

        return true;
    }
}
