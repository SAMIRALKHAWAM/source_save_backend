<?php

namespace App\Services;

use App\Exceptions\BaseException;
use App\Exceptions\CustomExceptionWithMessage;
use App\Exceptions\NotFoundException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\FlareClient\Http\Exceptions\NotFound;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseService
{
    protected $model;

    public function getAll($where = [])
    {
        return $this->model::where($where)->get();
    }

    public function getOne($id)
    {
        $object = $this->model::find($id);
        if (!$object) {
          return 'e';
        }
        return $object;
    }

    public function create($data)
    {
        return $this->model::create($data);
    }

    public function update($id, $data)
    {
        $object = $this->model::find($id);
        if (!$object) {
            return 'e';
        }
        $object->update($data);
        return $object;
    }

    public function delete($id)
    {
        $object = $this->model::find($id);
        if (!$object) {
            return 'e';
        }
        return $object->delete();
    }
}
