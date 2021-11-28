<div class="form-group">
    <label for="titulo">Título *</label>
    <input type="text" name="titulo" id="titulo" class="form-control" required value="{{ old('titulo', $post->title) }}">
</div>

<div class="form-group">
    <label for="file">Imagen</label>
    <input type="file" name="imagen" id="imagen" class="form-control-file">

    @if( ! empty($post->image) )
    <div class="py-3">
        <small class="d-block mb-1 text-danger">Imagen actual</small>
        <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" height="auto" width="100%">
    </div>
    @endisset
</div>

<div class="form-group">
    <label for="contenido">Contenido *</label>
    <textarea name="contenido" id="contenido" rows="6" class="form-control" required>{{ old('contenido', $post->body) }}</textarea>
</div>

<div class="form-group">
    <label for="iframe">Contenido Embebido</label>
    <textarea name="iframe" id="iframe" rows="6" class="form-control" required>{{ old('iframe', $post->iframe) }}</textarea>
</div>

<div class="form-group">
    <input type="submit" value="Enviar" class="btn btn-sm btn-primary">
</div>
