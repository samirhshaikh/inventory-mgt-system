<?php

namespace App\Traits;

use App\Models\BaseModel;
use Illuminate\Http\Request;

trait TableActions
{
    /**
     * Set the keys for a save update query.
     *
     * @param BaseModel $model
     * @param Request $request
     * @return String
     */
    protected function changeRecordStatus(BaseModel $model, Request $request)
    {
        $record = $model::where('Id', $request->get('Id'))
            ->first();

        if ($record) {
            $record->IsActive = $request->get('value');
            $record->save();

            return '';
        } else {
            return 'record_not_found';
        }
    }

    /**
     * Set the keys for a save update query.
     *
     * @param Array $tables_to_check
     * @param Mix $column_ids
     * @param Mix $id_value
     * @return Boolean
     */
    protected function foreignReferenceFound($tables_to_check, $column_ids, $id_value) {
        if (!is_array($column_ids)) {
            $column_ids = [$column_ids];
        }

        foreach ($tables_to_check as $table) {
            $class = '\\App\\Models\\' . $table;
            $class_reference = new $class();

            foreach ($column_ids as $column_id) {
                $reference = $class_reference->where($column_id, $id_value)
                    ->get();
                if ($reference->count()) {
                    return true;
                }
            }
        }

        return false;
    }
}
