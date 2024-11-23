<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseCRUDController extends Controller
{

    protected $service;
    protected $createRequest;
    protected $updateRequest;

    protected function index()
    {
        $res = $this->service->getAll();
        return \SuccessData(__('custom.Success'),$res);
    }

    protected function get_one($id)
    {
        $res = $this->service->getOne($id);
        return $this->sendResponse(data: $res, message: __('custom.Success'));
    }

    protected function store(Request $request)
    {
        $data = app($this->createRequest)->validated();
        $res = $this->service->create($data);
        return \SuccessData(__('custom.Success'), $res);
    }

    protected function update(Request $request, $id)
    {
        $data = app($this->updateRequest)->validated();
        $res = $this->service->update($id, $data);
        return $this->sendResponse(data: $res, message: __('custom.Success'));
    }

    protected function destroy($id)
    {
        $res = $this->service->delete($id);
        return $this->sendResponse(data: $res, message: __('custom.Success'));
    }
}
