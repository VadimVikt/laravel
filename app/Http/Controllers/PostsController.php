<?php

namespace App\Http\Controllers;

use App\Post;
use App\Tag;

class PostsController extends Controller
{
    public function index()
    {
        $posts = Post::with('tags')->latest()->get();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function store()
    {
        $this->validate(request(), [
            'slug' => 'required',
            'title' => 'required',
            'short_description' => 'required',
            'body' => 'required',
        ]);

        Post::create(request()->all());

        flash('Статья успешно добавлена');

        return redirect('/posts');

    }

    public function edit(Post $post)
    {
        return view('/posts.edit', compact('post'));
    }

    public function update(Post $post)
    {
//        dd($post);
        $attributes = request()->validate([
            'slug' => 'required',
            'title' => 'required',
            'short_description' => 'required',
            'body' => 'required'
        ]);
        $post->update($attributes);
        /** @var Collection $taskTags */
        $postTags = $post->tags->keyBy('name');

        $tags = collect(explode(',', request('tags')))->keyBy(function ($item) {return $item; });

        $tagsToAttach = $tags->diffKeys($postTags);
        $tagsToDetach = $postTags->diffKeys($tags);

        foreach ($tagsToAttach as $tag) {
            $tag = Tag::firstOrCreate(['name' => $tag]);
            $post->tags()->attach($tag);
        }
        foreach ($tagsToDetach as $tag) {
            $post->tags()->detach($tag);
        }
        flash('Статья успешно обновлена', 'alert alert-primary');
        return redirect('/posts');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        flash('Статья удалена', 'danger');

        return redirect('/posts');
    }
}

