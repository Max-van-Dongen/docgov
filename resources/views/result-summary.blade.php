@php use App\Models\File; @endphp
@php
    function convertBulletPointsInSummary($inputText): string
    {
        // Split the text into lines
        $lines = explode("\n", $inputText);

        // Process each line: wrap in <li> if it starts with "- ", leave the rest unchanged
        $insideBulletList = false;
        $outputLines = [];

        foreach ($lines as $line) {
            if (preg_match('/^- (.*)/', $line, $matches)) {
                // Line starts with "- ", wrap it in <li>
                if (!$insideBulletList) {
                    // Start a new <ul> if not already inside one
                    $outputLines[] = "<ul>";
                    $insideBulletList = true;
                }
                $outputLines[] = "<li>" . $matches[1] . "</li>";
            } else {
                // If we encounter a non-bullet point line, close the <ul> if necessary
                if ($insideBulletList) {
                    $outputLines[] = "</ul>";
                    $insideBulletList = false;
                }
                $outputLines[] = $line; // Add the regular line as-is
            }
        }

        // Close the <ul> if the text ends with bullet points
        if ($insideBulletList) {
            $outputLines[] = "</ul>";
        }

        // Combine the processed lines back into a single string
        return implode("\n", $outputLines);
    }
        $file = File::find($id);
        $tags = $file->keywords;
        $related_files = File::whereHas('keywords', function ($query) use ($tags) {
        $query->whereIn('keywords.id', $tags->pluck('id'));
    })->where('files.id', '!=', $file->id) // Exclude the current file
    ->distinct() // Ensure no duplicate files
    ->get();
        $bbcode = new ChrisKonnertz\BBCode\BBCode();
    $rendered = $bbcode->render($file->summary);
    $rendered = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $rendered);//replace **a** with <b>a</b>
    $rendered = preg_replace('/###\s*(.*)/', '<h3>$1</h3>', $rendered);//replace ### with h3
    $rendered = convertBulletPointsInSummary($rendered);
@endphp
@extends('result-layout')

@section("pdf-content")
            <div class="col-md-8">
                <div class="card shadow-sm p-4">
                    <h5>Summary</h5>
                    <p class="text-muted">
                        {!! $rendered !!}
                    </p>
                    <button class="btn btn-outline-primary mt-3">What does this mean for me?</button>
                </div>
            </div>

@endsection
