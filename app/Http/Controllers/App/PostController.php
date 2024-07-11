<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdatePostFormRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = $this->post->get();

        return view('app.posts.index', compact('posts'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdatePostFormRequest $request)
    {

        $post = $request->user()
                        ->posts()
                        ->create($request->all());

        return redirect()
                    ->route('post.index')
                    ->with('Cadastro realizado com sucesso!');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = $this->post->find($id);

        return view('app.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = $this->post->find($id);

        $post->update($request->all());

        return redirect()
        ->route('post.index')
        ->with('Atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
