<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryParamRequest;
use App\Http\Requests\StoreStockEntryRequest;
use App\Http\Requests\UpdateStockEntryRequest;
use App\Http\Traits\HelperTrait;
use App\Services\StockEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockEntryController extends Controller
{
    use HelperTrait;

    private $service;

    public function __construct(StockEntryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(QueryParamRequest $request): JsonResponse
    {
        try {

            $data = $this->service->index($request);

            return $this->successResponse($data, 'StockEntry data retrieved successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockEntryRequest $request): JsonResponse
    {
        try {
            $resource = $this->service->store($request);

            return $this->successResponse($resource, 'StockEntry created successfully!', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to create StockEntry', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $resource = $this->service->show($id);

            return $this->successResponse($resource, 'StockEntry retrieved successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to retrieve StockEntry', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockEntryRequest $request, int $id): JsonResponse
    {
        try {
            $resource = $this->service->update($request, $id);

            return $this->successResponse($resource, 'StockEntry updated successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to update StockEntry', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->destroy($id);

            return $this->successResponse(null, 'StockEntry deleted successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to delete StockEntry', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stockProducts(Request $request, $id): JsonResponse
    {
        try {
            $data = $this->service->stockProducts($id);

            return $this->successResponse($data, 'Data retrieved successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
