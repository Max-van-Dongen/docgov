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
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Environmental Impact of Gas Extraction</h6>
                        <p class="card-text text-muted">This report details the projected environmental impact of continued gas
                            extraction in Groningen...</p>
                        <div class="card-footer text-muted">
                            <span class="badge bg-primary">NAM</span>
                            <span class="badge bg-primary">Gas Extraction</span>
                            <span class="badge bg-primary">Groningen</span>
                            <span class="badge bg-primary">2016</span>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Compensation for Damage Caused by Gas Extraction</h6>
                        <p class="card-text text-muted">Details the compensation processes available for residents affected by gas
                            extraction...</p>
                        <div class="card-footer text-muted">
                            <span class="badge bg-primary">Compensation</span>
                            <span class="badge bg-primary">Earthquake Damage</span>
                            <span class="badge bg-primary">2021</span>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Safety and Future of Gas Extraction</h6>
                        <p class="card-text text-muted">This article discusses safety measures and future plans related to gas
                            extraction in Groningen...</p>
                        <div class="card-footer text-muted">
                            <span class="badge bg-primary">Safety</span>
                            <span class="badge bg-primary">Future Plans</span>
                            <span class="badge bg-primary">2022</span>
                        </div>
                    </div>
                </div>
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
