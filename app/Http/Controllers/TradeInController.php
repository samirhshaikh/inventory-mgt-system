<?php

namespace App\Http\Controllers;

use App\Exceptions\RecordNotFoundException;
use App\Services\TradeInService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradeInController extends BaseController
{
    public function delete(Request $request): JsonResponse
    {
        $tradein_service = new TradeInService();

        try {
            $tradein_service->delete($request->get('purchase_id'));

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
