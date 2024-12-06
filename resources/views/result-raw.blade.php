@php use App\Models\File; @endphp
@php

    $file = File::find($id);
@endphp
@extends('result-layout')

@section("pdf-content")

    <div class="col-md-8">
        <div class="card shadow-sm p-4">
            <iframe src="{{ $file->url }}" frameborder="0" height="700px"></iframe>
        </div>
    </div>
@endsection
