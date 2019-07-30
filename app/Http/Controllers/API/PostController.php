<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Post;
use Validator;

class PostController extends Controller
{

    public function __construct(){
        $this->middleware(['auth:api'])->except('index', 'show');
        $this->middleware(['resourceModification'])->only('update', 'destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = Post::orderby('id', 'desc')->with('users')->paginate(isset($request->per_page) ? $request->per_page : 10);
        return response()->json(['posts' => $posts])->setStatusCode(Response::HTTP_OK); // 200
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validating title and body field
        $validator = Validator::make($request->all(), [
            'title'=>'required|max:100',
            'body' =>'required',
            ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY); // 422            
        }
        $post = new Post;
        $post->user_id = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $post->title = $request->title;
        $post->body = $request->body;

        if($post->save()){
            return response()->json(['post' => $post])->setStatusCode(Response::HTTP_CREATED); // 201
        }
        return response()->json(['error' => "Unable to create"])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);        // 500
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json(['post'=>$post])->setStatusCode(Response::HTTP_OK); // 200 
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
        $validator = Validator::make($request->all(), [
            'title'=>'required|max:100',
            'body' =>'required',
            ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY); // 422                        
        }
        $input = $request->all();
        $post->title = $input['title'];
        $post->body = $input['body'];
        $post->status = isset($input['status']) ? $input['status'] : $post->status;
        if($post->save()){
            return response()->json(['post' => $post])->setStatusCode(Response::HTTP_OK); // 200
        }
        return response()->json(['error' => "Unable to create"])->setStatusCode(Response::HTTP_NOT_MODIFIED);        // 304
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null)->setStatusCode(Response::HTTP_NO_CONTENT);        // 204
    }
}
