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
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:root-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:root-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:root-delete', ['only' => ['destroy']]);

        
    }

    public function getClentesCache(){
        $getApiClients = Http::get(env('API_URL') . env('API_CLIENTS'));
        return $getApiClients->json()[0];
    }


    public function index(Request $request)
    {
        $data = User::orderBy('id', 'ASC')->paginate(5);

        $config_layout = [
            'title-section' => 'Configuracion > Usuarios',
            'breads' => 'Configuracion > Usuarios',
            'btn-create' => 'users.create'
        ];

        return view('config.users.index', compact('data', 'config_layout'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        $clientes = $this->getClentesCache();
        foreach($clientes as $cliente){
            $data[$cliente['id']] =  $cliente['name'];
        }
        
        // return $data;

        $config_layout = [
            'title-section' => 'Usuarios > Nuevo Usuario',
            'breads' => 'Configuracion > Usuarios > Nuevo Usuario',
            'btn-back' => 'users.index'
        ];

        return view('config.users.create', compact('roles', 'config_layout', 'data'));
    }

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

        if (isset($input['ve_clientes'])) {
            $input['ve_clientes'] = json_encode($input['ve_clientes']);
        }

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'Creado con exito');
    }

    public function show($id)
    {

        $user = User::find($id);
        $clientes = $this->getClentesCache();

        if (Gate::check('root-list') && $user->ve_clientes === null) {
            $user->ve_clientes = [];
        } else {
            $user->ve_clientes = json_decode($user->ve_clientes, true);
        }

        $config_layout = [
            'title-section' => 'Usuarios: ' . $user->name,
            'breads' => 'Configuracion > Usuarios > ' . $user->name,
            'btn-back' => 'users.index',
            'btn-edit' => 'users.edit',
        ];

        return view('config.users.show', compact('user', 'config_layout', 'clientes'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $clients = $this->getClentesCache();

        if (Gate::check('root-list') && $user->ve_clientes === null) {
            $user->ve_clientes = [];
        } else {
            $user->ve_clientes = json_decode($user->ve_clientes, true);
        }

        $config_layout = [
            'title-section' => 'Usuarios > Editar: ' . $user->name,
            'breads' => 'Configuracion > Usuarios > Editar: ' . $user->name,
            'btn-back' => 'users.index',
            'btn-edit' => 'users.edit',
            'btn-password-reset' => 'users.reset-password',
        ];

        return view('config.users.edit', compact('user', 'roles', 'userRole', 'config_layout', 'clients'));
    }



    public function resetPassword($id){

        $user = User::find($id);
        $user->password = Hash::make(12345678);
        $user->password_changed_at = null;
        $user->save();
        return back();
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        if (isset($input['ve_clientes'])) {
            $input['ve_clientes'] = json_encode($input['ve_clientes']);
        } else {
            $input['ve_clientes'] = null;
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'Editado con exito');
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }


    public function getClienteUser(){

        if (Gate::check('root-list')) {
            $data_clientes = Http::get(env('API_URL') . env('API_CLIENTS'))->json()[0];
        } else {
            if (auth()->user()->ve_clientes !== null) {
                $clientes = json_decode(auth()->user()->ve_clientes, true);
                $response = Http::get(env('API_URL') . env('API_CLIENTS'))->json()[0];
                foreach ($response as $key => $value) {
                    if (in_array($value['id'], $clientes)) {
                        $data_clientes[] = $value;
                    }
                }
            } else {
                $data_clientes = [];
            }
        }

        return $data_clientes;
    }
}
