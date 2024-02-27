<?php

namespace App\Traits;

use App\Exceptions\RecordNotFoundException;
use App\Http\Requests\IdRequest;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait TableActions
{
    /**
     * Set the keys for a save update query.
     *
     * @param BaseModel $model
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    protected function changeRecordStatus(
        BaseModel $model,
        IdRequest $request
    ): bool {
        $record = $model::where("id", $request->get("id"))->first();

        if ($record) {
            $record->IsActive = $request->get("value");
            $record->save();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * Set the keys for a save update query.
     *
     * @param array $tables_to_check
     * @param mixed $column_ids
     * @param mixed $id_value
     * @return Boolean
     */
    protected function foreignReferenceFound(
        array $tables_to_check,
        $column_ids,
        $id_value
    ): bool {
        if (!is_array($column_ids)) {
            $column_ids = [$column_ids];
        }

        foreach ($tables_to_check as $table) {
            $class = "\\App\\Models\\" . $table;
            $class_reference = new $class();

            foreach ($column_ids as $column_id) {
                $reference = $class_reference
                    ->where($column_id, $id_value)
                    ->get();
                if ($reference->count()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Builder $records
     * @return int
     */
    protected function getTotalRecords(Builder $records): int
    {
        try {
            $all_records = $records
                ->addSelect(DB::raw("COUNT(*) as Record_Count"))
                ->get()
                ->first();

            return $all_records["Record_Count"];
        } catch (\Exception $e) {
            return 0;
        }
    }
}
