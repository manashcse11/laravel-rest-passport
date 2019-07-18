<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller {
    public $successStatus = 200;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function role_list()
    {
        $roles = Role::all(); 
        return response()->json(['roles' => $roles], $this-> successStatus); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function role_store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles'
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $role = Role::create(['name' => $input['name']]);
        if($role){
            return response()->json(['role' => $role], 200); 
        }
        return response()->json(['error' => "Unable to create a role"], 401);        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function permission_list()
    {
        $permissions = Permission::all(); 
        return response()->json(['permissions' => $permissions], $this-> successStatus); 
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permission_store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $permission = Permission::create(['name' => $input['name']]);
        if($permission){
            return response()->json(['permission' => $permission], $this-> successStatus);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function role_has_permissions(Request $request, Role $role){
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id'
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }
        $permission = Permission:: find($request['permission_id'])->firstOrFail();

        if($role->givePermissionTo($permission)){
            return response()->json(['success' => $role], $this-> successStatus);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign_user_to_role(Request $request, Role $role){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user = User:: find($request['user_id'])->firstOrFail();

        if($user->assignRole($role)){
            return response()->json(['success' => $user], $this-> successStatus);
        }
    }
}