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

    function truncateTextToTokenLimit($text, $tokenLimit)
    {
        // Approximate tokens per character for English (1 token ≈ 4 characters)
        $approxCharsPerToken = 4;

        // Calculate the character limit
        $charLimit = $tokenLimit * $approxCharsPerToken;

        // Truncate the text to the character limit
        if (strlen($text) > $charLimit) {
            $text = substr($text, 0, $charLimit);
        }

        return $text;
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
                    <button class="btn btn-outline-primary mt-3" id="summaryButton">What does this mean for me?</button>
                </div>
            </div>
            <div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="summaryModalLabel">Personalized Summary</h5>
                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="summaryContent">
                                <!-- Streaming content will be appended here -->
                                <p>Loading personalized summary...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
    <script>
        document.getElementById("summaryButton").addEventListener("click", function () {
            // Open the modal
            const summaryModal = new mdb.Modal(document.getElementById("summaryModal"));
            summaryModal.show();

            // Clear previous content
            const summaryContent = document.getElementById("summaryContent");
            summaryContent.innerHTML = "<p>Loading personalized summary...</p>";

            const text = @json(truncateTextToTokenLimit($file->summary,10000));
            // Fetch the personalized summary from the backend
            fetch("/api/stream-personal-summary", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    text: text, // Replace with the text to summarize
                }),
            })
                .then((response) => {
                    if (!response.body) {
                        throw new Error("No response body");
                    }

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();

                    let partialData = "";

                    return reader.read().then(function processChunk({ done, value }) {
                        if (done) {
                            return;
                        }

                        // Decode the received chunk and append it to the buffer
                        partialData += decoder.decode(value, { stream: true });

                        // Process chunks to extract content
                        const lines = partialData.split("\n");
                        partialData = lines.pop(); // Save the last partial line for the next chunk

                        lines.forEach((line) => {
                            if (line.trim() === "data: [DONE]") {
                                // Ignore the "[DONE]" line
                                return;
                            }

                            if (line.trim().startsWith("data: ")) {
                                try {
                                    const parsed = JSON.parse(line.trim().substring(6));
                                    const content = parsed?.choices[0]?.delta?.content;

                                    if (content) {
                                        // Append content to the modal
                                        summaryContent.innerHTML += content;
                                    }
                                } catch (e) {
                                    console.error("Failed to parse line:", line, e);
                                }
                            }
                        });

                        return reader.read().then(processChunk);
                    });
                })
                .catch((error) => {
                    summaryContent.innerHTML = `<p class="text-danger">Failed to load summary: ${error.message}</p>`;
                });
        });


    </script>
@endsection
