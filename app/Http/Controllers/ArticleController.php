<?php

namespace App\Http\Controllers;

use App\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index()
    {
//        $articles = Article::all();
//        将最新文章排在最前
//        会调用Article Mode 类中的scopePublished()方法
        $articles = Article::latest()->published()->get();
        return view('articles.index',compact('articles'));
    }
    public  function show($id)
    {
//        $article = Article::find($id);
        $article = Article::findOrFail($id);
        dd($article->published_at->diffForHumans());
        return view('articles.show', compact('article'));
    }

    public function create()
    {
        return view('articles.create');
    }
    public function store(Requests\CreateArticleRequest $request)
    {
//        接受post数据
//        存入数据库
//        重定向
//        validate  第二个参数是 rules
//        Article::create($request->all());
        Article::create(array_merge(['user_id'=>\Auth::user()->id],$request->all()));
        dd(\Auth::user()->id);
        return redirect('articles');
    }
    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit', compact('article'));
    }

    public function update(Requests\CreateArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->all());
        return redirect('/articles');
    }

}
