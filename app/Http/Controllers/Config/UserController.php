<?php

namespace App\Http\Controllers\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:root-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:root-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:root-delete', ['only' => ['destroy']]);
    }


    public function index(Request $request)
    {
        $data = User::orderBy('id','ASC')->paginate(5);

        $config_layout = [
            'title-section' => 'Configuracion > Usuarios',
            'breads' => 'Configuracion > Usuarios',
            'btn-create' => 'users.create'
        ];


        return view('config.users.index',compact('data', 'config_layout'))
            ->with('i', ($request->input('page', 1) - 1) * 5);

            
    }
    

    public function create()
    {
        $roles = Role::pluck('name','name')->all();

        $clients = DB::table('clients')->pluck('name','id');

        // return $clients;

        $config_layout = [
            'title-section' => 'Usuarios > Nuevo Usuario',
            'breads' => 'Configuracion > Usuarios > Nuevo Usuario',
            'btn-back' => 'users.index'
        ];


        return view('config.users.create',compact('roles', 'config_layout', 'clients'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // return $request;
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        if(isset($input['ve_clientes'])){
            $input['ve_clientes'] = json_encode($input['ve_clientes']);
        }

        

        // return $input;
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        $clients = DB::table('clients')->select('name','id')->get();
        $user->ve_clientes = json_decode($user->ve_clientes, true);


        $config_layout = [
            'title-section' => 'Usuarios: '.$user->name,
            'breads' => 'Configuracion > Usuarios > '.$user->name,
            'btn-back' => 'users.index',
            'btn-edit' => 'users.edit',
        ];


        return view('config.users.show',compact('user', 'config_layout', 'clients'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

       

        $user->ve_clientes = json_decode($user->ve_clientes, true);

        
        $clients = DB::table('clients')->select('name','id')->get();
         


        $config_layout = [
            'title-section' => 'Usuarios > Editar: '.$user->name,
            'breads' => 'Configuracion > Usuarios > Editar: '.$user->name,
            'btn-back' => 'users.index',
            'btn-edit' => 'users.edit',
        ];
    
        return view('config.users.edit',compact('user', 'roles', 'userRole', 'config_layout', 'clients'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
}
