<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryParamRequest;
use App\Http\Requests\StoreSupportAccountRequest;
use App\Http\Requests\UpdateSupportAccountRequest;
use App\Services\SupportAccountService;
use App\Http\Traits\HelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupportAccountController extends Controller
{
    use HelperTrait;

    private $service;

    public function __construct(SupportAccountService $service)
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

            return $this->successResponse($data, 'SupportAccount data retrieved successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Something went wrong' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupportAccountRequest $request): JsonResponse
    {
        try {
            $resource = $this->service->store($request);

            return $this->successResponse($resource, 'SupportAccount created successfully!', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to create SupportAccount' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $resource = $this->service->show($id);

            return $this->successResponse($resource, 'SupportAccount retrieved successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to retrieve SupportAccount' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupportAccountRequest $request, int $id): JsonResponse
    {
        try {
            $resource = $this->service->update($request, $id);

            return $this->successResponse($resource, 'SupportAccount updated successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to update SupportAccount' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->destroy($id);

            return $this->successResponse(null, 'SupportAccount deleted successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to delete SupportAccount' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function paymentComplete(Request $request, int $id): JsonResponse
    {
        try {
            $this->service->paymentComplete($request ,$id);

            return $this->successResponse(null, 'Payment completed successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to complete payment' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
