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
                <div class="list-group">
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

@endsection
