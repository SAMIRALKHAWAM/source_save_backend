<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\CompareRequest;

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

    public function compareFiles(CompareRequest $request){
        $arr = Arr::only($request->validated(),['path1','path2']);
        $replacedPath1 = preg_replace('/storage/', 'public', $arr['path1'], 1);
        $replacedPath2 = preg_replace('/storage/', 'public', $arr['path2'], 1);


        if (!Storage::exists($replacedPath1) || !Storage::exists($replacedPath2)) {
            return response()->json(['message' => 'Files not found'], 404);
        }


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


        $differences = $differ->diffToArray($originalContent, $updatedContent);


        $updatedRows = array_filter($differences, function ($diff) {
            return $diff[1] === 2;
        });

        $formattedRows = array_map(function ($diff) {
            return $diff[0];
        }, $updatedRows);

        return array_values($formattedRows);
    }


    private function comparePdfFiles($originalPath, $updatedPath)
    {
        try {
            $originalText = Pdf::getText($originalPath, env('PDF_TO_TEXT_PATH'));
            if ($originalText === false) {
                throw new Exception("Failed to extract text from the original PDF.");
            }


            $updatedText = Pdf::getText($updatedPath, env('PDF_TO_TEXT_PATH'));
            if ($updatedText === false) {
                throw new Exception("Failed to extract text from the updated PDF.");
            }


            if (trim($originalText) === trim($updatedText)) {
                return 'The PDF files are identical.';
            }


            $originalLines = explode("\n", trim($originalText));
            $updatedLines = explode("\n", trim($updatedText));


            $addedLines = array_diff($updatedLines, $originalLines);
            $removedLines = array_diff($originalLines, $updatedLines);


            $result = "The PDF files have differences:\n";
            if (!empty($addedLines)) {
                $result .= "\n[Added Lines]\n" . implode("\n", $addedLines);
            }
            if (!empty($removedLines)) {
                $result .= "\n\n[Removed Lines]\n" . implode("\n", $removedLines);
            }

            return $result;

        } catch (Exception $e) {
            return "Error comparing PDF files: " . $e->getMessage();
        }
    }



}
