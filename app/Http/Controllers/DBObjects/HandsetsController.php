<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\HandsetsDatatable;
use App\Exceptions\DuplicateNameException;
use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveHandsetRequest;
use App\Models\Handsets;
use App\Services\HandsetsService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HandsetsController extends BaseController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new HandsetsDatatable();

        $order_by = session(
            "app_settings.datatable.sorting.handsets.column",
            Arr::get($table->options(), "sorting.default")
        );
        $order_direction = strtoupper(
            session(
                "app_settings.datatable.sorting.handsets.direction",
                Arr::get($table->options(), "sorting.direction")
            )
        );

        $handsets = Handsets::selectRaw(
            "HandsetMaster.*, ManufactureMaster.Name as manufacturer, ColorMaster.Name as color, modelmaster.Name as model"
        )
            ->join("ManufactureMaster", "ManufactureMaster.Id", "=", "MakeId")
            ->join("ColorMaster", "ColorMaster.Id", "=", "ColorId")
            ->join("modelmaster", "modelmaster.Id", "=", "ModelId")
            ->orderBy($order_by, $order_direction)
            ->get();

        $handsets = $handsets->map->transform();

        $return = [];
        $return["rows"] = $handsets->transform(function ($row, $key) use (
            $table
        ) {
            return $table->rowTransformer($row, $key);
        });

        return $return;
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function changeActiveStatus(IdRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $handsets_service->changeActiveStatus($request);

            return $this->sendOK([], "status_changed");
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $response = [];
            $response["record"] = $handsets_service->getSingle($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param SaveHandsetRequest $request
     * @return JsonResponse
     */
    public function save(SaveHandsetRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $id = $handsets_service->save($request);

            return $this->sendOK(
                [
                    "id" => $id,
                ],
                self::RECORD_SAVED
            );
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (DuplicateNameException $e) {
            return $this->sendError(
                self::DUPLICATE_NAME,
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $handsets_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (ReferenceException $e) {
            return $this->sendError(
                self::RECORD_REFERENCE_FOUND,
                [],
                JsonResponse::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkDuplicateName(Request $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $handsets_service->checkDuplicateName($request);

            return $this->sendOK([]);
        } catch (DuplicateNameException $e) {
            return $this->sendError(
                self::DUPLICATE_NAME,
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
