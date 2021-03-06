@extends('layouts.dashboard')

@section('content')
<div class="row justify-content-between">
    <div class="col-auto">
        <h1>Creazione nuovo post</h1>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-primary">Tutti i post</a>
    </div>
</div>
<div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
<form action="{{ route("admin.posts.store")}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="title">Titolo</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Inserisci il titolo" value="{{ old("title") }}">
        @error("title")
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <label for="content">Contenuto</label>
        <textarea name="content" cols="30" rows="10" class="form-control @error('title') is-invalid @enderror" placeholder="Scrivi qui...">{{ old("content") }}</textarea>
        @error("content")
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <label for="image">Immagine</label>
        <input type="file" name="image" />
    </div>
    <div class="form-group">
        <label for="category_id">Categoria</label>
        <select name="category_id" class="@error('category_id') is-invalid @enderror">
            <option hidden>Seleziona la categoria</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $category->id == old("category_id") ? "selected": "" }}>{{ $category->name }}</option>
            @endforeach
        </select>
        @error("category_id")
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <h3>Tags</h3>
        @foreach ($tags as $tag)
            <input type="checkbox" value="{{$tag->id}}" name="tags[]"
            {{ in_array($tag->id, old("tags", [])) ? "checked" : "" }}/>
            <label class="mr-3">{{$tag->name}}</label>
        @endforeach
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success">Crea post</button>
    </div>
</form>

@endsection