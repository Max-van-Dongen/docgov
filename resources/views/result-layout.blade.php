@php use App\Models\File; @endphp
@extends('frontend')
@php
        $file = File::find($id);
        $tags = $file->keywords;
        $people = $file->people;
//        $related_files = File::whereHas('keywords', function ($query) use ($tags) {
//        $query->whereIn('keywords.id', $tags->pluck('id'));
//    })->where('files.id', '!=', $file->id) // Exclude the current file
//    ->distinct() // Ensure no duplicate files
//    ->get();
        $related_files = $file->relatedFiles;
//        dd($related_files);

@endphp
@section("title")
{{$file->title}}
@endsection
@section("content")

    <div class="container my-4">

        <h4 class="mt-3">{{$file->title}} <span class="text-muted">| {{ \Carbon\Carbon::parse($file['original_date'])->format('d-m-Y')  }}</span> </h4>
        <!-- Main Content -->
        <div class="row">
            <!-- Content Section -->
            @yield('pdf-content')

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
                    <a href="{{ $file->url }}" class="btn btn-white mx-1" download>
                        <i class="ti ti-download"></i> Download PDF
                    </a>
                </div>

                <!-- Toggle Buttons -->
                <div class="d-flex justify-content-around mb-4">
                    <a href="/result/{{$file->id}}"
                       class="btn {{ Request::is('result/'.$file->id) ? 'btn-primary text-white' : ' border' }}">
                        Summary
                    </a>
                    <a href="/result/{{$file->id}}/raw"
                       class="btn {{ Request::is('result/'.$file->id.'/raw') ? 'btn-primary text-white' : ' border' }}">
                        Raw Data
                    </a>

                </div>


                <div class="card shadow-sm p-4 mb-4">
                    <h5>Document Info:</h5>
                    <p class="card-text"><b>Document:</b> {{ $file->type_document  }}</p>
                    <p class="card-text"><b>Category:</b> {{$file->type_category}}</p>
                    <p class="card-text"><b>Published:</b> {{\Carbon\Carbon::parse($file->original_date)->format('d-m-Y')}}</p>
                </div>

                <div class="card shadow-sm p-4 mb-4">
                    <h6>Related Articles:</h6>
                    <ul class="list">
                        @if(count($related_files) > 10)
                            @foreach($related_files as $index => $r_file)
                                @php
                                    $matchedWords = json_decode($r_file->pivot->matched_words, true) ?? [];
                                    $highlightedTitle = app(\App\Models\FileRelevancy::class)
                                        ->highlightMatchedWords($r_file->title, $matchedWords);
                                @endphp

                                @if($index < 10)
                                <li>
                                    <a href="/result/{{$r_file->id}}" class="text-decoration-none">
                                        {!! $highlightedTitle !!} <span class="">| {{\Carbon\Carbon::parse($r_file['created_at'])->format('d-m-Y')}}</span>
                                    </a>
                                </li>
                                @endif
                            @endforeach

                            <div class="collapse" id="moreArticles">
                                <div>
                                    @foreach($related_files as $index2 => $r_file)
                                        @php
                                            $matchedWords = json_decode($r_file->pivot->matched_words, true) ?? [];
                                            $highlightedTitle = app(\App\Models\FileRelevancy::class)
                                                ->highlightMatchedWords($r_file->title, $matchedWords);
                                        @endphp
                                        @if($index2 >= 10)
                                            <li>
                                                <a href="/result/{{$r_file->id}}" class="text-decoration-none">
                                                    {!! $highlightedTitle !!} <span class="">| {{\Carbon\Carbon::parse($r_file['created_at'])->format('d-m-Y')}}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <p>
                                <button class="btn btn-primary mt-2" type="button" data-mdb-collapse-init data-mdb-target="#moreArticles" aria-expanded="false" aria-controls="moreArticles">
                                    More
                                </button>
                            </p>
                        @else
                            @forelse($related_files as $index => $r_file)
                                @php
                                    $matchedWords = json_decode($r_file->pivot->matched_words, true) ?? [];
                                    $highlightedTitle = app(\App\Models\FileRelevancy::class)
                                        ->highlightMatchedWords($r_file->title, $matchedWords);
                                @endphp

                                    <li>
                                        <a href="/result/{{$r_file->id}}" class="text-decoration-none">
                                            {!! $highlightedTitle !!} <span class="">| {{\Carbon\Carbon::parse($r_file['created_at'])->format('d-m-Y')}}</span>
                                        </a>
                                    </li>
                            @empty
                                No Related Articles
                            @endforelse
                        @endif
                    </ul>
                </div>


                <!-- Related Tags -->
                <div class="shadow-sm p-4 mb-4 card">
                    <h6>Related Tags:</h6>
                    <div class="text-center">
                        @forelse($tags as $tag)
                            <span class="badge rounded-pill badge-primary"><a href="/search?query={{$tag->word}}">{{$tag->word}}</a></span>
                        @empty
                            No Related Tags
                        @endforelse</div>
                </div>
                <!-- Related People -->
                <div class="shadow-sm p-4 mb-4 card">
                    <h6>Related People:</h6>
                    <div class="text-center">
                        @forelse($people as $person)
                            <span class="badge rounded-pill badge-primary"><a href="/search?query={{$person->name}}">{{$person->name}}</a></span>
                        @empty
                            No Mentioned People
                        @endforelse</div>
                </div>
            </div>
        </div>
    </div>

@endsection
