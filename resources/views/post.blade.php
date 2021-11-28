@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">

                    <h5 class="card-title font-weight-bold text-uppercase my-4">{{ $post->title }}</h5>

                    @if ($post->image)
                        <img src="{{ $post->get_image }}" alt="{{$post->title}}" class="card-img-top mb-3">
                    @endif

                    @if ($post->iframe)
                        <div class="embed-responsive embed-responsive-16by9 mb-3">
                            {!! $post->iframe !!} {{-- show HTML --}}
                        </div>
                    @endif

                    <p class="card-text">
                        {{ $post->body }}
                    </p>
                    <p class="text-muted mb-0">
                        <em>
                            &ndash; {{ $post->user->name}}
                        </em>
                        {{ $post->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
