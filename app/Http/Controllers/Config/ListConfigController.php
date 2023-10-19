<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ListConfigController extends Controller
{

    public function index(Request $request)
    {

        $config_layout = [
            'title-section' => 'Configuracion > Listas',
            'breads' => 'Configuracion > Listas',
            'btn-create' => 'list-config.create',
            'btn-back' => 'clients.show'
        ];


        $data = [
            [
                "id" => 1,
                "discador" => "https://discador1.com",
                "nombre" => "discador 1",
                "IVR" => true,
                'lista' => '',
                'prefix' => 'ACSA',
                "active" => 1
            ],
            [
                "id" => 2,
                "discador" => "https://discador2.com",
                "nombre" => "discador 2",
                "IVR" => false,
                'lista' => '',
                'prefix' => 'ACSA',
                "active" => 1
            ],
            [
                "id" => 3,
                "discador" => "https://discador3.com",
                "nombre" => "discador 3",
                "IVR" => true,
                'lista' => '',
                'prefix' => 'ACSA',
                "active" => 1
            ],
        ];


        $id_cliente = Cache::get('cliente')->id;

        // return ($data);
        return view('config.list.index', compact('data', 'config_layout', 'request', 'id_cliente'));
    }

    public function create(Request $request)
    {

        // return $request;

        // ! Lista de discadores
        

        $config_layout = [
            'title-section' => 'Listas > Nueva configuracion',
            'breads' => 'Configuracion > Nueva configuracion',
            'btn-back' => 'list-config.index'
        ];
        
        $data['prefix'] = $request->prefix;

        return view('config.list.create', compact('config_layout', 'data'));
    }

    public function show($id){


        $datas = [
            [
                "id" => 1,
                "discador" => "https://discador1.com",
                "nombre" => "discador 1",
                "IVR" => true,
                "active" => 1
            ],
            [
                "id" => 2,
                "discador" => "https://discador2.com",
                "nombre" => "discador 2",
                "IVR" => false,
                "active" => 1
            ],
            [
                "id" => 3,
                "discador" => "https://discador3.com",
                "nombre" => "discador 3",
                "IVR" => true,
                "active" => 1
            ],
        ];

        // return $datas;

        $data = [];

        foreach($datas as $value){
            if($value['id'] == $id){
                $data = $value;
            }
        }

        $config_layout = [
            'title-section' => 'Listas > Ver configuracion: '.$data['nombre'],
            'breads' => 'Configuracion > Ver configuracion: '.$data['nombre'],
            'btn-back' => 'list-config.index',
        ];

        return view('config.list.show', compact('data', 'config_layout'));
    }
}
