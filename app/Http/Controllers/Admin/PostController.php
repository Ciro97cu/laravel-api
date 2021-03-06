<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Tag;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // recupero tutti i post e li valorizzo in una variabile 
        $posts = Post::all();
        // ritorno la view index e passo la variabile precedente
        return view("admin.posts.index", compact("posts"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // per il form di creazione ritorno semplicemente la view create e passo le categorie e i tag
        $categories = Category::all();
        $tags = Tag::all();

        return view("admin.posts.create", compact("categories", "tags"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // effettuo la validazione sui campi della tabella e imposto anche dei messaggi di errore personalizzati
        $request->validate(
            [
                "title" => "required|max:255",
                "content" => "required",
                "category_id" => "required|exists:categories,id",
                'image' => 'required|mimes:jpg,png,jpeg|max:2048'
            ],
            [
                "title.required" => "The title is rquired",
                "title.max" => "First of all, Respect the rules",
                "content.required" => "The content is rquired",
                "category_id.required" => "The category is rquired",
                "image" => "Il file deve essere un'immagine"
            ]
        );
        // vado a recuperare tutti i dati e li valorizzo in una variabile
        $postData = $request->all();
        $img_path = Storage::put("uploads", $postData["image"]);
        $postData["cover"] = $img_path;
        $newPost = new Post();
        // li inserisco all'interno della tabella, compreso lo slug
        $newPost->fill($postData);
        $newPost->slug = Post::generateSlug($newPost->title);
        $newPost->save();
        // aggiungo i tag
        $newPost->tag()->sync($request["tags"]);
        // reindirizzo l'utente alla index
        return redirect()->route("admin.posts.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        // mostro la view show e passo i post
        return view("admin.posts.show", compact("post"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        // per il form di modifica ritorno semplicemente la view edit e passo le categorie e i tag
        $categories = Category::all();
        $tags = Tag::all();
        return view("admin.posts.edit", compact("post", "categories", "tags"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        // effettuo la validazione sui campi della tabella e imposto anche dei messaggi di errore personalizzati
        $request->validate(
            [
                "title" => "required|max:255",
                "content" => "required",
                "category_id" => "required|exists:categories,id",
                'image' => 'required|mimes:jpg,png,jpeg|max:2048'
            ],
            [
                "title.required" => "The title is rquired",
                "title.max" => "First of all, Respect the rules",
                "content.required" => "The content is rquired",
                "category_id.required" => "The category is rquired",
                "image" => "Il file deve essere un'immagine"
            ]
        );
        // vado a recuperare tutti i dati e li valorizzo in una variabile
        $postData = $request->all();
        Storage::delete($post->cover);
        $img_path = Storage::put("uploads", $postData["image"]);
        $postData["cover"] = $img_path;
        // li inserisco all'interno della tabella, compreso lo slug
        $post->fill($postData);
        $post->slug = Post::generateSlug($post->title);
        // modifico i tag
        $post->tag()->sync($request["tags"]);
        // effettuo l'update
        $post->update();
        // reindirizzo l'utente alla index
        return redirect()->route("admin.posts.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        // per la cancellazione del post eseguo direttamente il delete e reindirizzo l'utente alla index
        $post->tag()->sync([]);
        $post->delete();
        return redirect()->route("admin.posts.index");
    }
}
