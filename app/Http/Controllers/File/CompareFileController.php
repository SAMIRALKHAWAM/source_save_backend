<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\CompareRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Spatie\PdfToText\Pdf;

class CompareFileController extends Controller
{

    public function compareFiles(CompareRequest $request){
        $arr = Arr::only($request->validated(),['path1','path2']);
        $replacedPath1 = preg_replace('/storage/', 'public', $arr['path1'], 1);
        $replacedPath2 = preg_replace('/storage/', 'public', $arr['path2'], 1);

        // Check if the file exists
        if (!Storage::exists($replacedPath1) || !Storage::exists($replacedPath2)) {
            return response()->json(['message' => 'Files not found'], 404);
        }

        // Get the full path to the file
        $fullPath1 = Storage::path($replacedPath1);
        $fullPath2 = Storage::path($replacedPath2);

        $differences = $this->getDifferences($fullPath1, $fullPath2);


        return response()->json([
            'message' => 'Comparison completed successfully.',
            'differences' => $differences,
        ]);
    }

    private function getDifferences($path1, $path2)
    {
        if (str_ends_with($path1, '.csv')) {
            return $this->compareCsvFiles($path1, $path2);
        }

        if (str_ends_with($path1, '.txt')) {
            return $this->compareTextFiles($path1, $path2);
        }

        if (str_ends_with($path1, '.pdf')) {
            return $this->comparePdfFiles($path1, $path2);
        }
    }


    private function compareTextFiles($path1, $path2)
    {
        $originalContent = file_get_contents($path1);
        $updatedContent = file_get_contents($path2);
        $outputBuilder = new UnifiedDiffOutputBuilder("--- Original\n+++ Updated\n");
        $differ = new Differ($outputBuilder);
        return $differ->diffToArray($originalContent, $updatedContent);
    }

    private function compareCsvFiles($originalPath, $updatedPath)
    {
        $originalCsv = Reader::createFromPath($originalPath, 'r');
        $updatedCsv = Reader::createFromPath($updatedPath, 'r');

        $originalCsv->setHeaderOffset(0);
        $updatedCsv->setHeaderOffset(0);

        $originalRecords = iterator_to_array($originalCsv->getRecords());
        $updatedRecords = iterator_to_array($updatedCsv->getRecords());
        $outputBuilder = new UnifiedDiffOutputBuilder("--- Original\n+++ Updated\n");
        return [
            'original' => $originalRecords,
            'updated' => $updatedRecords,
            'diff' => (new Differ($outputBuilder))->diffToArray(json_encode($originalRecords), json_encode($updatedRecords)),
        ];
    }

    private function comparePdfFiles($originalPath, $updatedPath)
    {
        // Using Spatie's PdfToText
        $originalText = Pdf::getText($originalPath);
        $updatedText = Pdf::getText($updatedPath);
        $outputBuilder = new UnifiedDiffOutputBuilder("--- Original\n+++ Updated\n");
        $differ = new Differ($outputBuilder);
        return $differ->diffToArray($originalText, $updatedText);
    }
}
