@extends('frontend')

@section("content")

    <!-- Main Content -->
    <div class="row">
        <!-- Filters (Left Column) -->
        <div class="col-md-3">
            <div class="ms-3">
                <h6 class="mb-3">Filters</h6>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action active">All Regions</button>
                    <button class="list-group-item list-group-item-action">Groningen</button>
                    <button class="list-group-item list-group-item-action">Stakeholders</button>
                    <button class="list-group-item list-group-item-action">Economic Affairs</button>
                </div>
            </div>
        </div>

        <!-- Results (Center Column) -->
        <div class="col-md-6">
            <div class="mb-4">
                <h5 class="mb-3">Results</h5>

                @if($results->isEmpty())
                    <p class="text-muted">No results found for your search.</p>
                @else
                    @foreach($results as $result)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">{{ $result['title'] }}</h6>
                                <p class="card-text text-muted">{!!  $result['summary']  !!}</p>
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
                    <button class="btn btn-outline-primary mb-2 active">2024</button>
                    <button class="btn btn-outline-primary mb-2">2023</button>
                    <button class="btn btn-outline-primary mb-2">2022</button>
                    <button class="btn btn-outline-primary mb-2">2021</button>
                    <button class="btn btn-outline-primary mb-2">2020</button>
                    <button class="btn btn-outline-primary mb-2">2019</button>
                </div>
            </div>
        </div>
    </div>

@endsection
