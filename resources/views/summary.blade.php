@extends('frontend',["search" => true, "buttons" => true])

@section('content')
    <div class="d-flex justify-content-center align-items-center">
        <div class="rounded border p-4 w-50" id="summaryContainer">
            <h4 class="mb-3 text-center">Quick Summary</h4>
            <div id="summaryContent" class="text-muted"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        let totalText = "";
        function MarkdownText() {
            // Append rendered markdown to the summary container
            summaryContent.innerHTML = marked.parse(totalText);

        }
        async function fetchSummary(query) {
            const url = `/api/stream-results-summary?q=${encodeURIComponent(query)}`;

            fetch(url)
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

                        // Process chunks line by line
                        const lines = partialData.split("\n");
                        partialData = lines.pop(); // Save the last partial line for the next chunk

                        lines.forEach((line) => {
                            if (line.trim() === "data: [DONE]") {
                                // Stream is complete
                                return;
                            }

                            if (line.trim().startsWith("data: ")) {
                                try {
                                    // Parse JSON after "data: "
                                    const parsed = JSON.parse(line.trim().substring(6));
                                    const content = parsed?.choices?.[0]?.delta?.content;

                                    if (content) {
                                        totalText += content;
                                        MarkdownText();
                                        // Render the markdown content
                                    }
                                } catch (e) {
                                    console.error("Failed to parse line:", line, e);
                                }
                            }
                        });

                        // Read the next chunk
                        return reader.read().then(processChunk);
                    });
                });
        }



        // Automatically fetch summary based on ?q=...
        fetchSummary('{{ request()->query("query") }}');
    </script>
@endsection
