<?php

namespace App\Services;

use App\Exceptions\DuplicateNameException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveObjectTypeNameRequest;
use App\Models\BaseModel;
use App\Traits\SearchTrait;
use App\Traits\TableActions;
use Illuminate\Http\Request;

class ObjectTypeNameService
{
    use TableActions, SearchTrait;

    private $model;
    private $id_column;

    /**
     * ObjectTypeNameService constructor.
     * @param BaseModel $model
     * @param string $id_column
     */
    public function __construct(BaseModel $model, string $id_column)
    {
        $this->model = $model;
        $this->id_column = $id_column;
    }

    /**
     * @param string $order_by
     * @param string $order_direction
     * @param string $search_text
     * @return array
     */
    public function getAll(
        string $order_by,
        string $order_direction,
        string $search_text = ''
    ): array
    {
        $records = $this->model;

        if ($search_text != '') {
            $fields_to_search = [
                'Name',
                'DATE_FORMAT(CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $search_text);
        }

        $records = $records->orderBy($order_by, $order_direction);

        //Get total records
        $total_records = $this->getTotalRecords(clone $records);

        return [
            'total_records' => $total_records,
            'records' => $records
        ];
    }

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(IdRequest $request)
    {
        $record = $this->model::where('Id', $request->get('Id'))
            ->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    public function changeActiveStatus(IdRequest $request): bool
    {
        try {
            return $this->changeRecordStatus($this->model, $request);
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param SaveObjectTypeNameRequest $request
     * @return int
     * @throws DuplicateNameException
     * @throws RecordNotFoundException
     */
    public function save(SaveObjectTypeNameRequest $request): int
    {
        //Check for duplicate name
        if ($this->isDuplicateName($request->get('Name'), $request->get('Id', 0))) {
            throw new DuplicateNameException();
        }

        $record = $this->model::where('Id', $request->get('Id'))
            ->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$record->count()) {
                throw new RecordNotFoundException;
            }

            $record = $record->first();
        } else {
            $record = $this->model;

            $record->CreatedBy = session('user_details.UserName');
            $record->IsActive = 1;
        }

        $record->Name = $request->get('Name');
        $record->UpdatedBy = session('user_details.UserName');
        $record->save();

        return $request->get('operation', 'add') == 'edit'
            ? $request->get('Id')
            : $this->model::lastInsertId();
    }

    /**
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     * @throws ReferenceException
     */
    public function delete(IdRequest $request): bool
    {
        //Check whether the record exist or not
        $record = $this->model::where('Id', $request->get('Id'))
            ->get();

        if ($record->count()) {
            //Check whether the record is used as a reference in other tables.
            $tables_to_check = ['PhoneStock', 'Handsets'];    //'Repair'
            if ($this->foreignReferenceFound($tables_to_check, $this->id_column, $request->get('Id'))) {
                throw new ReferenceException;
            }

            $this->model::where('Id', $request->get('Id'))
                ->delete();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param Request $request
     * @return bool
     * @throws DuplicateNameException
     */
    public function checkDuplicateName(Request $request): bool
    {
        if ($this->isDuplicateName($request->get('Name'), $request->get('Id', 0))) {
            throw new DuplicateNameException;
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    private function isDuplicateName(string $name, int $id = 0): bool
    {
        //Check whether the record exists or not
        if (!empty($id)) {
            $record = $this->model::where('Id', $id)->get();

            if (!$record->count()) {
                return false;
            }
        }

        $record = $this->model::whereRaw('LOWER(Name) = ?', [strtolower($name)]);

        if (!empty($id)) {
            $record = $record->where('id', '!=', $id);
        }

        $record = $record->get();

        return $record->count() ? true : false;
    }
}
