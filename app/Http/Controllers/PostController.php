<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Controllers\SlugServices;
use App\Models\Post;
use Cviebrock\EloquentSluggable\Services\SlugService;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view('blog.index')->with('posts',Post::orderBy('updated_at','DESC')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     $request->validate([
         'title'=>'required',
         'description'=>'required',
         'image'=>'required',
         
        //  'image'=>'required |mimes : png,jpg,jpeg'
     
     ]);
        // ..how to dipaly image
        // a destination path
        // creating a path
        // request
     if($request->image !== null){
        if($request->hasFile('image'))
    
    {
        $destination_path='public/images/posts';
        $image =$request->file('image');
        $image_name=$image->getClientOriginalName();
        $path=$request->file('image')->storeAs($destination_path,$image_name);
        $request->image=$image_name;}
        else{

            echo "error";
        }
    }
     $slug=SlugService::createSlug( post::class,'slug',$request->title);
    
    Post::create([
        'title'=>$request->input('title'),
        'description'=>$request->input('description'),
        'image_path'=>$image_name,
        $slug=>SlugService::createSlug( post::class,'slug',$request->title),
        'user_id'=>auth()->user()->id
    ]);
    return redirect('/blog')->with ('message','your post has been added.');
     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {

        return view('blog.show')->with('post',Post::where('slug', $slug)->first());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        return view('blog.edit')->with('post',Post::where('slug',$slug)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        Post::where('slug',$slug)->update([
        'title'=>$request->input('title'),
        'description'=>$request->input('description'),
        // 'user_id'=>auth()->user()->id ,
        $slug=>SlugService::createSlug( post::class,'slug',$request->title),
        'user_id'=>auth::user()->id 
        ]);
        return redirect('/blog')->with('message','your post has been updated !');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $post=Post::where('slug',$slug);
        $post->delete();
        return redirect('/blog')->with('message','your post has been deleted');
    }
}
