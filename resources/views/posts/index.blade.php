@extends('layouts.app')

@section('content')
<div class="container">
    <div class="">
        <div class="card">
            <div class="card-header">
                Artículos
                <a href="{{ route('posts.create') }}" class="btn btn-sm btn-primary float-right">Crear</a>
            </div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Imagen</th>
                            <th colspan="2">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($posts) === 0)
                            <tr>
                                <td colspan="4">Aún no se publicó ningún artículo. Sé el primero en pubicar.</td>
                            </tr>

                        @else

                            @foreach ($posts as $post)
                                <tr>
                                    <td>{{ $post->id }}</td>
                                    <td>{{ $post->title }}</td>
                                    <td>
                                        @if(!empty($post->image))
                                            <p>
                                                <a class="btn btn-outline-success btn-sm" data-toggle="collapse" href="#imagenCollapse{{$post->id}}" role="button" aria-expanded="false" aria-controls="imagenCollapse{{$post->id}}">
                                                    Ver
                                                </a>
                                            </p>
                                            <div class="collapse" id="imagenCollapse{{$post->id}}">
                                                <div class="card card-body">
                                                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{$post->title}}" width="200" height="200">
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-dark">Sin Imagen</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('posts.edit', $post) }}" class="bnt btn-primary btn-sm">Editar</a>
                                    </td>
                                    <td>
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="submit" value="Eliminar" class="btn btn-danger btn-sm" onclick="return confirm('¿Desea eliminar...?');">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
