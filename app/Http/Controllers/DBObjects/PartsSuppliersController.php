<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveSupplierRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartsSuppliersController extends SuppliersController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $this->table_name = "parts_suppliers";
        return parent::getData($request);
    }

    /**
     * @param IdRequest $request
     * @return Response
     */
    public function changeActiveStatus(IdRequest $request): Response
    {
        $this->table_name = "parts_suppliers";
        return parent::changeActiveStatus($request);
    }

    /**
     * @param IdRequest $request
     * @return Response
     */
    public function getSingle(IdRequest $request): Response
    {
        $this->table_name = "parts_suppliers";
        return parent::getSingle($request);
    }

    /**
     * @param SaveSupplierRequest $request
     * @return Response
     */
    public function save(SaveSupplierRequest $request): Response
    {
        $this->table_name = "parts_suppliers";
        return parent::save($request);
    }

    /**
     * @param IdRequest $request
     * @return Response
     */
    public function delete(IdRequest $request): Response
    {
        $this->table_name = "parts_suppliers";
        return parent::delete($request);
    }
}
