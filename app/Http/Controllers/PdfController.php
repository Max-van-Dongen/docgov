<?php
namespace App\Http\Controllers;

use App\Services\PdfService;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    protected $pdfService;
    protected $openAIService;

    public function __construct(PdfService $pdfService, OpenAIService $openAIService)
    {
        $this->pdfService = $pdfService;
        $this->openAIService = $openAIService;
    }

    public function summarize(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:20480', // Max size 20MB
        ]);

        try {
            $filePath = $request->file('pdf')->getPathname();
            $text = $this->pdfService->extractText($filePath);

            $summary = $this->openAIService->summarizeText($text);

            return response()->json(['summary' => $summary]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
