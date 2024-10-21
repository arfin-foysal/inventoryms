<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Http\Traits\HelperTrait;
use Illuminate\Http\Response;

class CommonController extends Controller
{
    use HelperTrait;

    public function __construct() {}

    public function fileUpload(FileUploadRequest $request)
    {
        try {
            $resource = $this->ftpFileUpload($request, 'file', 'image');

            return $this->successResponse($resource, 'File uploaded successfully!', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 'Failed to upload file', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
