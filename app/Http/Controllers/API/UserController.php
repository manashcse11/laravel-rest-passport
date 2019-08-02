<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Validator;

class UserController extends Controller
{

    public function __construct(){
        $this->middleware(['isAdmin'])->only('index', 'store');
        $this->middleware(['resourceModification'])->only('update', 'destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all(); 
        return response()->json(['users' => $users])->setStatusCode(Response::HTTP_OK); // 200
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
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY); // 422            
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password); 

        if($user->save()){
            return response()->json(['user' => $user])->setStatusCode(Response::HTTP_CREATED); // 201
        }
        return response()->json(['error' => "Unable to create"])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);        // 500   
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json(['user'=>$user])->setStatusCode(Response::HTTP_OK); // 200 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY); // 422                        
        }
        $input = $request->all();
        $user->name = $input['name'];
        $user->email = $input['email'];
        if($user->save()){
            return response()->json(['user' => $user])->setStatusCode(Response::HTTP_OK); // 200
        }
        return response()->json(['error' => "Unable to create"])->setStatusCode(Response::HTTP_NOT_MODIFIED);        // 304
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null)->setStatusCode(Response::HTTP_NO_CONTENT);        // 204
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function posts(Request $request, User $user)
    {
        return response()
            ->json([
                'user' => $user
                , 'posts' => $user->posts()->orderBy('id', 'desc')->paginate(isset($request->per_page) ? $request->per_page : 10)
            ])->setStatusCode(Response::HTTP_OK); // 200
    }
}
