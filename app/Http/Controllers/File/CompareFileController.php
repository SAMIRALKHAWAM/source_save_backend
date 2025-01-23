<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\CompareRequest;

use App\Services\CompareFileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Spatie\PdfToText\Pdf;
use function Illuminate\Events\queueable;

class CompareFileController extends Controller
{

    protected $service ;

    public function __construct(CompareFileService  $service)
    {
        $this->service = $service;
    }

    public function compareFiles($path1,$path2)
    {
        return $this->service->compareFiles($path1,$path2);
    }

}
