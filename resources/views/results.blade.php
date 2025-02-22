@php
    // Flatten all keywords from the results
    $allKeywords = $results->pluck('keywords')->flatten();
    $keywordCounts = $allKeywords->countBy();
    
    $topKeywords = $keywordCounts->sortDesc()->keys()->take(10);
    $topKeywordsWithCounts = $keywordCounts->sortDesc()->take(10);
@endphp

@extends('frontend',["search" => true, "buttons" => true])
@section("content")

    <!-- Main Content -->
    <div class="row">
        <!-- Filters (Left Column) -->
        <div class="col-md-3">
            <div class="ms-3">
                <h6 class="mb-3">Types</h6>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action active" onclick="applyTypeFilter('allType')" id="allType">All Types</button>

                    @foreach($results->pluck('type_document')->flatten()->unique()->filter(function ($value) {return $value != "";})->take(10) as $keyword)
                        <button class="list-group-item list-group-item-action" onclick="applyTypeFilter('{{ $keyword }}')" id="{{ $keyword }}">
                            {{ $keyword }}
                        </button>
                    @endforeach
                </div>
                <h6 class="my-3">Categories</h6>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action active" onclick="applyCategoryFilter('allCat')" id="allCat">All Categories</button>

                    @foreach($results->pluck('type_category')->flatten()->unique()->filter(function ($value) {return $value != "";})->take(10) as $keyword)
                        <button class="list-group-item list-group-item-action" onclick="applyCategoryFilter('{{ $keyword }}')" id="{{ $keyword }}">
                            {{ $keyword }}
                        </button>
                    @endforeach
                </div>
                <h6 class="my-3">Keywords</h6>
                <div class="list-group" id="keywords-container" data-keywords="{{ json_encode($topKeywords) }}">
                    <button class="list-group-item list-group-item-action active" onclick="applyFilter('allKey')" id="allKey">All Keywords</button>

                    @foreach($topKeywords as $keyword)
                        <button class="list-group-item list-group-item-action" onclick="applyFilter('{{ $keyword }}')" id="{{ $keyword }}">
                            {{ $keyword }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Results (Center Column) -->
        <div class="col-md-6">
            <div class="mb-4">
                <!-- Word Web PH -->
                <button id="toggle-word-web" class="btn btn-primary mb-4" style="display: none;">Hide Word Web</button>
                <div id="word-web-container" class="mb-4" style="display: none;"></div>
                <div id="results-container" result-data="{{ json_encode($results->take(10)) }}"></div>
                <div id="keywords-count-container" result-data="{{ json_encode($topKeywordsWithCounts) }}"></div>
                <h5 class="mb-3">Results ({{$results->count()}})</h5>

                @if($results->isEmpty())
                    <p class="text-muted">No results found for your search.</p>
                @else
                    @foreach($results as $result)
                        <div class="card mb-3" data-year="{{ \Carbon\Carbon::parse($result['created_at'])->format('Y') }}" data-type-document="{{ $result['type_document'] }}" data-type-category="{{ $result['type_category'] }}">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <a class="text-primary text-decoration-none" href="/result/{{$result['id']}}">
                                        {{ $result['title'] }}
                                    </a>
                                </h5>
                                <p class="card-text text-secondary">
                                    <a class="text-muted text-decoration-none" href="/result/{{$result['id']}}">
                                        {{ $result['short_desc'] }}
                                    </a>
                                </p>
                                <p class="card-text text-muted">
                                    <span class="fw-semibold">{{ $result['type_document'] }}</span> |
                                    <span>{{ $result['type_category'] }}</span> |
                                    <span>{{ \Carbon\Carbon::parse($result['created_at'])->format('Y-m-d') }}</span>
                                </p>
                                <div class="mt-3">
                                    @foreach($result['keywords'] as $keyword)
                                        <span class="badge bg-primary">{{ ucwords($keyword) }}</span>
                                    @endforeach
                                    @foreach($result['people'] as $person)
                                        <span class="badge bg-secondary">{{ ucwords($person) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Timeline (Right Column) -->
        <div class="col-md-3">
            <div class="me-3">
                <h6 class="mb-3">Timeline</h6>
                <div class="d-flex flex-column">
                    <button class="btn btn-primary mb-2 active" onclick="applyYearFilter('allYear')" id="allYear">All Years</button>

                    @foreach($results->pluck('created_at')->map(function($date) {
                        return \Carbon\Carbon::parse($date)->year;
                    })->unique()->sortDesc() as $year)
                        <button class="btn btn-outline-primary mb-2" onclick="applyYearFilter('{{ $year }}')" id="{{ $year }}">
                            {{ $year }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>


    </div>
    <!-- idk hoe dit werkt dus heb het in een apart <script /> ding gedaan -->
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        // const results = JSON.parse(document.getElementById("results-container").getAttribute("result-data"));
        // console.log("Results:", results);
        // const keywordsCount = JSON.parse(document.getElementById("keywords-count-container").getAttribute("result-data"));
        // console.log("Keywords w Count:", keywordsCount);

        document.addEventListener("DOMContentLoaded", () => {
            const keywordsContainer = document.getElementById("keywords-container");
            const urlParams = new URLSearchParams(window.location.search);
            const mainSearch = urlParams.get('query') || urlParams.get('keyword') || "Geen idee";
            const inDepth = urlParams.get('indepth') || 1;
            if (inDepth == 0) {
                document.getElementById("word-web-container").style.display = "block";
                document.getElementById("toggle-word-web").style.display = "block";
            }

            const keywords = parseKeywords(keywordsContainer, mainSearch);
            if (keywords.length > 0 && mainSearch !== "Geen idee") {
                renderWordWeb(mainSearch, keywords);
            }
            else {
                document.getElementById("word-web-container").style.display = "none";
                document.getElementById("toggle-word-web").style.display = "none";
            }

            const toggleButton = document.getElementById("toggle-word-web");
            toggleButton.addEventListener("click", toggleWordWebVisibility);
        });

        function parseKeywords(container, mainSearch) {
            // Soms wordt er een object ipv een array teruggegeven, dit lost dat op
            try {
                const rawKeywords = JSON.parse(container.getAttribute("data-keywords"));
                console.log("Keywords:", rawKeywords);
                console.log("Main search:", mainSearch);

                if (Array.isArray(rawKeywords)) {
                    return rawKeywords.filter(keyword => keyword !== mainSearch);
                } else if (typeof rawKeywords === 'object') {
                    return Object.values(rawKeywords).filter(keyword => keyword !== mainSearch);
                } else {
                    console.error("Invalid keywords format: expected an array or object.");
                    return [];
                }
            } catch (error) {
                console.error("Error parsing 'data-keywords':", error);
                return [];
            }
        }

        function renderWordWeb(mainSearch, keywords) {
            const keywordWithCount = JSON.parse(document.getElementById("keywords-count-container").getAttribute("result-data"))
            console.log("Keywords w Count:", keywordWithCount);
            const graphData = {
                nodes: [{ id: mainSearch, group: 1 }, ...keywords.map(k => ({ id: k, group: 2 }))],
                links: keywords.map(k => ({ source: mainSearch, target: k }))
            };

            const width = 550, height = 550, radius = 205;
            const svg = d3.select("#word-web-container").append("svg").attr("width", width).attr("height", height);
            const centerX = width / 2, centerY = height / 2;
            
            // Posities berekenen
            const angleForIndex = i => (i / (graphData.nodes.length - 1)) * (2 * Math.PI);
            const calculatePosition = (i, center, radius, axis) => i === 0 ? center : center + radius * Math[axis](angleForIndex(i));

            const link = svg.append("g").selectAll("line").data(graphData.links).enter().append("line")
                .attr("stroke-width", 2)
                .attr("stroke", "#aaa")
                .attr("x1", d => getNodeX(d.source, mainSearch))
                .attr("y1", d => getNodeY(d.source, mainSearch))
                .attr("x2", d => getNodeX(d.target, mainSearch))
                .attr("y2", d => getNodeY(d.target, mainSearch));

            const node = svg.append("g").selectAll("circle").data(graphData.nodes).enter().append("circle")
                .attr("r", d => getNodeSize(d.id))
                .attr("fill", d => d.group === 1 ? "#FFFFFF" : "#154273")
                .attr("stroke", d => d.group === 1 ? "#3c3c3c" : "none") // Rand bij middelste ding voor zichtbaarheid light mode
                .attr("stroke-width", d => d.group === 1 ? 2 : 0) // Rand bij middelste ding voor zichtbaarheid light mode
                .style("cursor", d => d.group !== 1 ? "pointer": null)
                .attr("cx", (d, i) => calculatePosition(i, centerX, radius, "cos"))
                .attr("cy", (d, i) => calculatePosition(i, centerY, radius, "sin"))
                .on("click", (event, d) => handleNodeClick(d, mainSearch)); // Navigate naar keyword
                
            const text = svg.append("g").selectAll("text").data(graphData.nodes).enter().append("text")
                .attr("font-size", d => getFontSize(d.id)) 
                .attr("fill", d => d.group === 1 ? "black" : "white")
                .attr("dy", 4)
                .attr("text-anchor", "middle")
                .style("cursor", d => d.group !== 1 ? "pointer": null)
                .attr("x", (d, i) => calculatePosition(i, centerX, radius, "cos"))
                .attr("y", (d, i) => calculatePosition(i, centerY, radius, "sin"))
                .on("click", (event, d) => handleNodeClick(d, mainSearch))
                .each(function (d, i) {
                    const maxNodeSize = getNodeSize(d.id); 
                    const maxWidth = maxNodeSize * 1.8; // Adjust width relative to node size
                    const lines = splitTextIntoLines(d.id, maxWidth);

                    const lineHeight = 12;
                    const textElement = d3.select(this);

                    lines.forEach((line, lineIndex) => {
                        textElement.append("tspan")
                            .text(line)
                            .attr("x", calculatePosition(i, centerX, radius, "cos")) 
                            .attr("y", calculatePosition(i, centerY, radius, "sin") + lineIndex * lineHeight - (lines.length - 1) * lineHeight / 2)
                            .attr("dy", 4);
                    });
                });
            
            // Zorgt ervoor dat de tekst in t ding past, kan beter
            function getFontSize(text) {
                const maxFontSize = 16, minFontSize = 11, maxLength = 4;
                return Math.max(minFontSize, maxFontSize - Math.floor(text.length / maxLength));
            }

            function getNodeSize(id) {
                const sizes = [42, 52, 62];
                if (id === mainSearch) {
                    return 62;
                }
                if (typeof keywordWithCount === 'object') {
                    const keywordsFiltered = Object.fromEntries(Object.entries(keywordWithCount).filter(([key]) => key !== mainSearch));
                    const values = Object.values(keywordsFiltered);
                    const highestCount = Math.max(...values);
                    const lowestCount = Math.min(...values);

                    if (id in keywordsFiltered) {
                        const count = keywordsFiltered[id];

                        if (highestCount === lowestCount) {
                            return sizes[2]; 
                        }

                        // Adjust for large outliers
                        const logHighest = Math.log(highestCount);
                        const logLowest = Math.log(lowestCount > 0 ? lowestCount : 1);
                        const logCount = Math.log(count > 0 ? count : 1);
                        
                        const normalizedValue = (logCount - logLowest) / (logHighest - logLowest); 
                        const adjustedValue = Math.sqrt(normalizedValue);

                        // Scale the size based on the count relative to the highest count
                        if (adjustedValue >= 0.75) {
                            return sizes[2];
                        } else if (adjustedValue >= 0.40) {
                            return sizes[1];
                        } else {
                            return sizes[0];
                        }
                    }
                }
                return 62;
            }
            
            // Node posities berekenen
            function getNodeX(id, mainSearch) {
                const nodeIndex = graphData.nodes.findIndex(node => node.id === id);
                return calculatePosition(nodeIndex, centerX, radius, "cos");
            }
            function getNodeY(id, mainSearch) {
                const nodeIndex = graphData.nodes.findIndex(node => node.id === id);
                return calculatePosition(nodeIndex, centerY, radius, "sin");
            }

            // Navigeer naar nieuwe zoekopdracht
            function handleNodeClick(node, mainSearch) {
                if (node.id !== mainSearch) {
                    window.location.href = `/search?query=${encodeURIComponent(node.id)}&indepth=0`;
                }
            }

            function splitTextIntoLines(text, maxWidth) {
                const lines = [];
                const words = text.split(" ");
                const isOneWord = words.length === 1;

                function splitLongWord(word, separator = "-") {
                    let currentIndex = 0;
                    while (currentIndex < word.length) {
                        let nextIndex = currentIndex + 1;
                        while (nextIndex <= word.length &&getTextWidth(word.slice(currentIndex, nextIndex) + separator, "12px Arial") <= maxWidth) {
                            nextIndex++;
                        }
                        lines.push(word.slice(currentIndex, nextIndex - 1) + (nextIndex - 1 < word.length ? separator : ""));
                        currentIndex = nextIndex - 1;
                    }
                }

                if (isOneWord) {
                    if (text.includes("-")) {
                        text.split("-").forEach((part, i, parts) => {
                            const partWithHyphen = i < parts.length - 1 ? `${part}-` : part;
                            if (getTextWidth(partWithHyphen, "12px Arial") <= maxWidth) {
                                lines.push(partWithHyphen);
                            } else {
                                splitLongWord(partWithHyphen);
                            }
                        });
                    } else {
                        splitLongWord(text);
                    }
                } else {
                    let currentLine = "";
                    for (const word of words) {
                        const testLine = currentLine ? `${currentLine} ${word}` : word;
                        if (getTextWidth(testLine, "12px Arial") <= maxWidth) {
                            currentLine = testLine;
                        } else {
                            if (currentLine) lines.push(currentLine);
                            currentLine = word;
                        }
                    }
                    if (currentLine) lines.push(currentLine);
                }
                return lines;
            }

            function getTextWidth(text, font) {
                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                context.font = font;
                return context.measureText(text).width;
            }
        }

        function toggleWordWebVisibility() {
            const wordWebContainer = document.getElementById("word-web-container");
            wordWebContainer.style.display = wordWebContainer.style.display === "none" ? "block" : "none";
        }

    </script>
    <script>
        let activeFilters = {
            keyword: 'allKey',
            type: 'allType',
            category: 'allCat',
            year: 'allYear',
        };

        // Function to apply the keyword filter
        function applyFilter(keyword) {
            if (activeFilters.keyword) {
                document.getElementById(activeFilters.keyword).classList.remove("active")
            }
            activeFilters.keyword = keyword;
            document.getElementById(activeFilters.keyword).classList.add("active")
            updateResults();
        }

        // Function to apply the type filter
        function applyTypeFilter(type) {
            if (activeFilters.type) {
                document.getElementById(activeFilters.type).classList.remove("active")
            }
            activeFilters.type = type;
            document.getElementById(activeFilters.type).classList.add("active")
            updateResults();
        }

        // Function to apply the category filter
        function applyCategoryFilter(category) {
            if (activeFilters.category) {
                document.getElementById(activeFilters.category).classList.remove("active")
            }
            activeFilters.category = category;
            document.getElementById(activeFilters.category).classList.add("active")
            updateResults();
        }

        // Function to update results based on all active filters
        function updateResults() {
            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const matchesKeyword = activeFilters.keyword === 'allKey' || card.innerHTML.includes(activeFilters.keyword);
                const matchesType = activeFilters.type === 'allType' || card.getAttribute('data-type-document') === activeFilters.type;
                const matchesCategory = activeFilters.category === 'allCat' || card.getAttribute('data-type-category') === activeFilters.category;
                const matchesYear = activeFilters.year === 'allYear' || card.getAttribute('data-year') === activeFilters.year;

                if (matchesKeyword && matchesType && matchesCategory && matchesYear) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function applyYearFilter(year) {
            if (activeFilters.year) {
                document.getElementById(activeFilters.year).classList.remove("btn-primary")
                document.getElementById(activeFilters.year).classList.add("btn-outline-primary")
            }
            activeFilters.year = year;
            document.getElementById(activeFilters.year).classList.add("btn-primary")
            document.getElementById(activeFilters.year).classList.remove("btn-outline-primary")
            updateResults();
        }
    </script>
    <style>
        /* CSS god enzo */
        #word-web-container {
            width: 500px; 
            height: 500px; 
            margin: auto;
        }
    </style>


@endsection
