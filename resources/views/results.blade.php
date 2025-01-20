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
                <div class="list-group" id="keywords-container" data-keywords="{{ json_encode($results->pluck('keywords')->flatten()->unique()->take(10)) }}">
                    <button class="list-group-item list-group-item-action active" onclick="applyFilter('allKey')" id="allKey">All Keywords</button>

                    @foreach($results->pluck('keywords')->flatten()->unique()->take(10) as $keyword)
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
                <button id="toggle-word-web" class="btn btn-primary mb-4">Hide Word Web</button>
                <div id="word-web-container" class="mb-4"></div>
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
        document.addEventListener("DOMContentLoaded", () => {
            const keywordsContainer = document.getElementById("keywords-container");
            const urlParams = new URLSearchParams(window.location.search);
            const mainSearch = urlParams.get('query') || urlParams.get('keyword') || "Geen idee";

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
                .attr("r", 62)
                .attr("fill", d => d.group === 1 ? "#FFFFFF" : "#154273")
                .attr("stroke", d => d.group === 1 ? "#3c3c3c" : "none") // Rand bij middelste ding voor zichtbaarheid light mode
                .attr("stroke-width", d => d.group === 1 ? 2 : 0) // Rand bij middelste ding voor zichtbaarheid light mode
                .style("cursor", d => d.group !== 1 ? "pointer": null)
                .attr("cx", (d, i) => calculatePosition(i, centerX, radius, "cos"))
                .attr("cy", (d, i) => calculatePosition(i, centerY, radius, "sin"))
                .on("click", (event, d) => handleNodeClick(d, mainSearch)); // Navigate naar keyword
                
            const text = svg.append("g").selectAll("text").data(graphData.nodes).enter().append("text")
                .text(d => d.id)
                .attr("font-size", d => getFontSize(d.id)) 
                .attr("fill", d => d.group === 1 ? "black" : "white")
                .attr("dy", 4)
                .attr("text-anchor", "middle")
                .style("cursor", d => d.group !== 1 ? "pointer": null)
                .attr("x", (d, i) => calculatePosition(i, centerX, radius, "cos"))
                .attr("y", (d, i) => calculatePosition(i, centerY, radius, "sin"))
                .on("click", (event, d) => handleNodeClick(d, mainSearch)); // Navigate naar keyword
            
            // Zorgt ervoor dat de tekst in t ding past, kan beter
            function getFontSize(text) {
                const maxFontSize = 16, minFontSize = 11, maxLength = 4;
                return Math.max(minFontSize, maxFontSize - Math.floor(text.length / maxLength));
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
                    window.location.href = `/search?query=${encodeURIComponent(node.id)}`;
                }
            }
            
        }

        function toggleWordWebVisibility() {
            const wordWebContainer = document.getElementById("word-web-container");
            if (wordWebContainer.style.display === "none") {
                wordWebContainer.style.display = "block";
            } else {
                wordWebContainer.style.display = "none";
            }
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
