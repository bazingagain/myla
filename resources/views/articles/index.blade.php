@extends('layouts.app')
@section('content')
    <h1>Article</h1>
    <hr>
    @foreach($articles as $article)
        <h2><a href="{{action('ArticleController@show', [$article->id])}}">{{$article->title}}</a></h2>
        <article>
            <div class="body">
                {{$article->content}}
            </div>

        </article>
    @endforeach


@endsection

