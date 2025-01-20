@extends('frontend',["search" => true, "buttons" => true])

@section("content")




    <!-- Topics Section -->
    <div class="container text-center">
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <!-- Topic 1 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Huisvesting'">
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
        <div class="d-flex justify-content-end mt-4">
            <!-- <button class="btn btn-primary mt-4" onclick="window.location = '/browse'">
                <i class="ti ti-search"></i> Browse All Topics
            </button> -->
            <a href="browse" class="btn btn-link">
                <i class="ti ti-search"></i> Browse All Topics
            </a>
        </div>
    </div>

@endsection
