<?php

namespace App\Services;

use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PdfService
{
    public function extractText($filePath): string
    {
        try {
            $text = Pdf::getText($filePath);

//            if (trim($text) === '') {
//                $text = (new TesseractOCR($filePath))->run();
//            }

            return $text;
        } catch (\Exception $e) {
            throw new \Exception("Error processing PDF: " . $e->getMessage());
        }
    }
}
