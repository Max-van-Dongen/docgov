@php use App\Models\File; @endphp
@extends('frontend-result')
@php
        $file = File::find($id);
        $tags = $file->keywords;
        $related_files = File::whereHas('keywords', function ($query) use ($tags) {
        $query->whereIn('keywords.id', $tags->pluck('id'));
    })->where('files.id', '!=', $file->id) // Exclude the current file
    ->distinct() // Ensure no duplicate files
    ->get();
@endphp
@section("title")
{{$file->title}}
@endsection
@section("content")

    <div class="container my-4">

        <h4 class="mt-3">{{$file->title}}</h4>
        <!-- Main Content -->
        <div class="row">
            <!-- Content Section -->
            <div class="col-md-8">
                <div class="card shadow-sm p-4">
                    <iframe src="{{Storage::url($file->location)}}" frameborder="0" height="700px"></iframe>
                </div>
            </div>

            <!-- Sidebar -->

            <div class="col-md-4">
                <!-- Related Articles -->
                <div class="d-flex justify-content-between mb-4">
                    <!-- Subscribe Button -->
                    <button class="btn btn-white mx-1">
                        <i class="ti ti-bell"></i> Subscribe topic
                    </button>

                    <!-- Save Button -->
                    <button class="btn btn-white mx-1" onclick="window.print()">
                        <i class="ti ti-device-floppy"></i> Save
                    </button>

                    <!-- Download Button -->
                    <a href="{{Storage::url($file->location)}}" class="btn btn-white mx-1" download>
                        <i class="ti ti-download"></i> Download PDF
                    </a>
                </div>

                <!-- Toggle Buttons -->
                <div class="d-flex justify-content-around mb-4">
                    <a href="/result/{{$file->id}}"
                       class="btn {{ Request::is('result/'.$file->id) ? 'btn-primary text-white' : 'btn-light border' }}">
                        Summary
                    </a>
                    <a href="/result/{{$file->id}}/raw"
                       class="btn {{ Request::is('result/'.$file->id.'/raw') ? 'btn-primary text-white' : 'btn-light border' }}">
                        Raw Data
                    </a>

                </div>
                <div class="card shadow-sm p-4 mb-4">
                    <h6>Related Articles:</h6>
                    <ul class="list-unstyled">
                        @forelse($related_files as $r_file)
                            <li><a href="/result/{{$r_file->id}}" class="text-decoration-none">{{$r_file->title}}</a></li>
                        @empty
                            No Related Articles
                        @endforelse
                    </ul>
                </div>

                <!-- Related Tags -->
                <div class="shadow-sm p-4 card">
                    <h6>Related Tags:</h6>
                    <div class="text-center">
                        @forelse($tags as $tag)
                            <span class="badge rounded-pill badge-primary">{{$tag->word}}</span>
                        @empty
                            No Related Tags
                        @endforelse</div>
                </div>
            </div>
        </div>
    </div>

@endsection
