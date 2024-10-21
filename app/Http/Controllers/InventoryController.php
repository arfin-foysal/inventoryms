<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryParamRequest;
use App\Http\Traits\HelperTrait;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InventoryController extends Controller
{
    use HelperTrait;
    private $service;

    public function __construct(InventoryService $service)
    {
        $this->service = $service;
    }


    public function inventory(QueryParamRequest $request)
    {
        try {
            $data = $this->service->inventory($request);

            return $this->successResponse($data, 'StockEntry data retrieved successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function inventoryCheck(Request $request, $id)
    {
        try {
            $data = $this->service->inventoryCheck($id);

            return $this->successResponse($data, 'Data retrieved successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
}
