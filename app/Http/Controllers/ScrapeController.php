<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScrapeController extends Controller
{
    public static function scrapeData(): array|bool
    {
        $url = 'https://open.overheid.nl/zoekresultaten?text='.$_GET["q"].'&page=1&count=50';
        $response = Http::get($url);

        if ($response->failed()) {
            Log::error('Failed to fetch data from ' . $url, ['status' => $response->status()]);
            return false;
        }

        $html = $response->body();
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $data = [];

        $listItems = $xpath->query("//div[contains(@class, 'result--list')]//ul/li");

        foreach ($listItems as $item) {
            $pdfNode = $xpath->query(".//div//ul[contains(@class, 'list--linked')]//li/a", $item)->item(0);

            if ($pdfNode) {
                $titleNode = $xpath->query(".//h2[contains(@class, 'result--title')]/a", $item)->item(0);
                $metadataNodes = $xpath->query(".//ul[contains(@class, 'list--metadata')]/li", $item);

                $title = $titleNode ? $titleNode->nodeValue : '';
                $url = $pdfNode->getAttribute('href');
                $pdfName = $pdfNode->nodeValue;

                $metadata = [];
                foreach ($metadataNodes as $metaNode) {
                    $metadata[] = $metaNode->nodeValue;
                }

                $data[] = [
                    'title' => trim($title),
                    'pdf_url' => 'https://open.overheid.nl' . trim($url),
                    'type_document' => $metadataNodes[0]->nodeValue,
                    'type_category' => $metadataNodes[1]->nodeValue,
                    'original_date' => str_replace("Beschikbaar sinds: ","",$metadataNodes[2]->nodeValue),
                ];
            }
        }

        return $data;
    }
}
