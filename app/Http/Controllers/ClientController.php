<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estructura;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;

class ClientController extends Controller
{


    public function index()
    {

        $data = Client::all();

        //return $data;

        return view('clients/index', compact('data'));
    }


    public function searchCliente(Request $request)
    {
        $prefix = $request->prefix;
        $query = Estructura::select('COLUMN_NAME', 'COLUMN_TYPE', 'DATA_TYPE', 'TABLE_NAME')->where("PREFIX", '=', $prefix)->get();
        return $query;
    }

    public function show($id){


        $client = Client::find($id);

        $dataEstrategias = Client::select('e.*')
        ->join('estrategias as e', 'e.prefix_client', '=', 'clients.prefix')
        ->where('clients.id', '=', $id)
        ->get();

        //return $dataEstrategias;

        return view('clients/show', compact('client', 'dataEstrategias'));
    }
}
