@extends('frontend')

@section("content")

    <!-- Main Content -->
    <div class="row">
        <!-- Filters (Left Column) -->
        <div class="col-md-3">
            <div class="ms-3">
                <h6 class="mb-3">Filters</h6>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action active" onclick="applyFilter('')">All Keywords</button>

                    @foreach($results->pluck('keywords')->flatten()->unique()->take(10) as $keyword)
                        <button class="list-group-item list-group-item-action" onclick="applyFilter('{{ $keyword }}')">
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
                        <div class="card mb-3" data-created-at="{{ \Carbon\Carbon::parse($result['created_at'])->format('Y-m-d') }}">
                            <div class="card-body">
                                <h6 class="card-title">{{ $result['title'] }}</h6>
                                <p class="card-text text-muted">{{ $result['short_desc'] }}</p>
                                <div class="card-footer text-muted">
                                    @foreach($result['keywords'] as $keyword)
                                        <span class="badge bg-primary">{{ $keyword }}</span>
                                    @endforeach
                                    @foreach($result['people'] as $person)
                                        <span class="badge bg-secondary">{{ $person }}</span>
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
                    <button class="btn btn-outline-primary mb-2 active" onclick="applyYearFilter('')">All Years</button>

                    @foreach($results->pluck('created_at')->map(function($date) {
                        return \Carbon\Carbon::parse($date)->year;
                    })->unique()->sortDesc() as $year)
                        <button class="btn btn-outline-primary mb-2" onclick="applyYearFilter('{{ $year }}')">
                            {{ $year }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>


    </div>
    <script>
        let activeKeyword = ''; // Currently selected keyword
        let activeYear = '';    // Currently selected year

        function applyFilter(keyword) {
            activeKeyword = keyword; // Update the active keyword
            filterResults(); // Apply filtering
        }

        function applyYearFilter(year) {
            activeYear = year; // Update the active year
            filterResults(); // Apply filtering
        }

        function filterResults() {
            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const keywords = Array.from(card.querySelectorAll('.badge.bg-primary')).map(badge => badge.textContent);
                const createdAt = card.getAttribute('data-created-at'); // Get the creation year from the card

                const matchesKeyword = activeKeyword === '' || keywords.includes(activeKeyword);
                const matchesYear = activeYear === '' || createdAt.startsWith(activeYear);

                // Show card if it matches both filters, otherwise hide it
                if (matchesKeyword && matchesYear) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

@endsection
