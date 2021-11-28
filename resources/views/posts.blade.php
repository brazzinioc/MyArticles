@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @foreach ($posts as $post)

                <div class="card mb-4">
                    <div class="card-body">
                        {{--Show image or Ifram --}}
                        @if ($post->image)
                            <img src="{{ $post->get_image }}" alt="{{$post->title}}" class="card-img-top mb-3">
                        @elseif ($post->iframe)
                            <div class="embed-responsive embed-responsive-16by9 mb-3">
                                {!! $post->iframe !!} {{-- show HTML --}}
                            </div>
                        @endif

                        <h5 class="card-title font-weight-bold">{{ $post->title }}</h5>
                        <p class="card-text">
                            {{ $post->get_extract }}
                            <a href="{{ route('post', [ 'slug' => $post->slug ]) }}">Leer más</a>
                        </p>
                        <p class="text-muted mb-0">
                            <em>
                                &ndash; {{ $post->user->name}}
                            </em>
                            {{ $post->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>

            @endforeach

            {{ $posts->links() }} {{-- links is a method --}}

        </div>
    </div>
</div>
@endsection
