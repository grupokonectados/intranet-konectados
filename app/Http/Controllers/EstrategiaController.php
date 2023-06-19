<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use App\Models\Estructura;
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

        //return $request;

        $saveQuery = new Estrategia();
        $saveQuery->query = $request->query_text;
        $saveQuery->onlyWhere = $request->onlyWhere;
        $saveQuery->channels = $request->channels;
        $saveQuery->table_name = $request->table_name;
        $saveQuery->query_description = $request->query_description;
        $saveQuery->prefix_client = $request->prefix;
        $saveQuery->save();

        return redirect()->route('estrategia.index');
         
    }

    public function show($id){

        $data = Estrategia::find($id);
        return view('estrategias/show', compact('data'));
    }


    public function runQuery(Request $request){

        // return $request; //Verificar que datos llegan. 

        // Aqui se prueba la consulta y se pueden ver los resultados segun el filtro puesto
        $result = \DB::select($request['query']);

        // Extraemos los nombres de las columnas segun la el cliente que se vaya a utilizar        
        $table = Estructura::select('COLUMN_NAME')->where('TABLE_NAME', '=', $request['table_name'])->get();

        

        if(count($result) > 0){

            // se cuenta el total de hallados en la consulta anterior
            $counter = \DB::select("select count(*) as counter from ".$request['table_name']." where ".$request['where']); 

            //Calculos de factibilidad dependiendo del canal *PREGUNTAR ESTE CASO* 
            switch ($request['channel']) {
                case 1:
                    $factibilidad = \DB::select("select count(movil1) as cc from ".$request['table_name']." where ".$request['where']." and LENGTH(movil1) != 0");
                    break;
                case 2:
                    $factibilidad = \DB::select("select count(fijo1) as cc from ".$request['table_name']." where ".$request['where']." and LENGTH(fijo1) != 0");
                break;
                case 3:
                    $factibilidad = \DB::select("select count(email1) as cc from ".$request['table_name']." where ".$request['where']." and LENGTH(email1) != 0");
                break;
                
            }

            //En base al contador total se le resta el contador segun el canal / (cc = counter channels)
            $resto = $counter[0]->counter-$factibilidad[0]->cc;
            // Se calcula el procentaje y se formatea
            // number_format(int or float, numero de decimales, coma para los decimales, punto para los miles)
            $porcentaje = number_format(($resto/$counter[0]->counter)*100, 2, ',', '.');

            return [ 
                'contador' => $counter[0]->counter,  // Muestro el contador
                'result' => $result, // El resultado de la consulta 
                'table' => $table, // Los nombres de las columnas
                'resto' => $resto, // la resta entre la factibilidad y el contador
                'porcentaje' => $porcentaje //el procentaje de factibilidad
            ];
        }else{
            return [ 
                'result' => 0, // El resultado de la consulta 
                'message' => 'No hay nada que mostrar'
            ];
        }

        return $result;

        
        
    }
}
