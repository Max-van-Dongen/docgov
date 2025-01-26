@extends('frontend', ["search" => false])
@section("content")

<!-- Topics Section -->
<div class="container text-center">
    <!-- Weet het scrollen nog nie zeker -->
    <!-- <div class="topics-scroll-wrapper" style="overflow-y: auto; overflow-x:hidden; max-height: 500px; padding-right: 10px;"> -->
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <!-- Topic 1 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Huisvesting&indepth=0'">
                    <i class="ti ti-home-2 fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Housing & Properties</p>
                </button>
            </div>
            <!-- Topic 2 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Gezondheid&indepth=0'">
                    <i class="ti ti-clipboard-heart fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Health & Social Services</p>
                </button>
            </div>
            <!-- Topic 3 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Baan&indepth=0'">
                    <i class="ti ti-briefcase fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Jobs & Employment</p>
                </button>
            </div>
            <!-- Topic 4 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=Energie&indepth=0'">
                    <i class="ti ti-bolt fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Environment & Energy</p>
                </button>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-4" style="margin-top: 10px;">
            <!-- Topic 5 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=veiligheid&indepth=0'">
                    <i class="ti ti-prison fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Safety & Crime</p>
                </button>
            </div>
            <!-- Topic 6 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=jeugd&indepth=0'">
                    <i class="ti ti-baby-carriage fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Youth</p>
                </button>
            </div>
            <!-- Topic 7 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=cultureel&indepth=0'">
                    <i class="ti ti-building-bank fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Culture</p>
                </button>
            </div>
            <!-- Topic 8 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=belasting&indepth=0'">
                    <i class="ti ti-coin-euro fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Taxes</p>
                </button>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-4" style="margin-top: 10px;">
            <!-- Topic 9 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=wet&indepth=0'">
                    <i class="ti ti-gavel fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Legal</p>
                </button>
            </div>
            <!-- Topic 10 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=gemeente&indepth=0'">
                    <i class="ti ti-buildings fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Municipalities</p>
                </button>
            </div>
            <!-- Topic 11 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=welzijn&indepth=0'">
                    <i class="ti ti-activity fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Well-being</p>
                </button>
            </div>
            <!-- Topic 12 -->
            <div class="col">
                <button class="btn w-100 py-4 border shadow-sm text-center"
                        onclick="window.location = '/search?query=onderwijs&indepth=0'">
                    <i class="ti ti-vocabulary fs-1 mb-2 text-primary"></i>
                    <p class="mb-0">Education</p>
                </button>
            </div>
        </div>
    <!-- </div> -->
</div>

@endsection
