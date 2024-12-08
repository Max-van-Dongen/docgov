@extends('frontend',["search" => true])

@section("content")

    <!-- Quick Summary/In-Depth Switch -->
    <div class="d-flex justify-content-center mb-4">
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="summarySwitch" autocomplete="off" id="quickSummary" checked>
            <label class="btn btn-secondary" for="quickSummary">Quick Summary</label>

            <input type="radio" class="btn-check" name="summarySwitch" autocomplete="off" id="inDepth">
            <label class="btn btn-secondary" for="inDepth">In Depth</label>
        </div>
    </div>


    <!-- Topics Section -->
    <div class="container text-center">
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <!-- Topic 1 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Huis'">
                    <i class="ti ti-home-2 fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Housing & Properties</p>
                </button>
            </div>
            <!-- Topic 2 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Gezondheid'">
                    <i class="ti ti-clipboard-heart fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Health & Social Services</p>
                </button>
            </div>
            <!-- Topic 3 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Baan'">
                    <i class="ti ti-briefcase fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Jobs & Employment</p>
                </button>
            </div>
            <!-- Topic 4 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Energie'">
                    <i class="ti ti-bolt fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Environment & Energy</p>
                </button>
            </div>
        </div>
    </div>

@endsection
