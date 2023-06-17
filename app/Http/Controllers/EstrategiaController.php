<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use Illuminate\Http\Request;

class EstrategiaController extends Controller
{
    public function index(){

        $data = Estrategia::select('estrategias.*', 'c.name', 'c.id as client_id')
                    ->join('clients as c', 'c.prefix', '=', 'estrategias.prefix_client')
                    ->get();

        return view('estrategias/index', compact('data'));
    }


    public function create(){

        $data = Client::all();
        return view('estrategias/create', compact('data'));
    }


    public function saveEstrategia(Request $request){


        $saveQuery = new Estrategia();

        $saveQuery->showQue = $request->query_text;
        $saveQuery->channels = $request->channels;
        $saveQuery->query_description = $request->query_description;
        $saveQuery->prefix_client = $request->prefix;

        $saveQuery->save();


        return redirect()->route('estrategia.index');
         
    }
}
