<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use App\Models\Estructura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use SebastianBergmann\Diff\Diff;

class EstrategiaController extends Controller
{


    function __construct()
    {
        $this->middleware('permission:root-list|strategy-list', ['only' => ['index', 'show', 'queryResults']]);
        $this->middleware('permission:root-create|strategy-create', ['only' => ['create', 'store', 'saveEstrategia']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update', 'isActive', 'acceptedStrategy']]);
        $this->middleware('permission:root-delete|strategy-delete', ['only' => ['destroy', 'deleteStrategy', 'stopStrategy']]);
    }

    const PATH_API = '/estrategias';


    public function index()
    {

        $config_layout = [
            'title-section' => 'Estrategias',
            'breads' => 'Estrategias',
            'btn-create' => 'estrategia.create'
        ];

        /**
         * Metodo laravel
         */



        $data = Estrategia::select('estrategias.*', 'c.name', 'c.id as client_id')
            ->join('clients as c', 'c.prefix', '=', 'estrategias.prefix_client')
            ->get();




        /**
         * Metodo API
         */


        // $response = Http::get(env('API_URL').self::PATH_API);
        // $data = $response->json();

        //return $data;


        return view('estrategias/index', compact('data', 'config_layout'));
    }


    public function create()
    {

        $config_layout = [
            'title-section' => 'Crear Estrategia',
            'breads' => 'Estrategias > Crear Estrategia',
            'btn-back' => 'estrategia.index'
        ];


        $data = Client::all();
        return view('estrategias/create', compact('config_layout', 'data'));
    }


    public function saveEstrategia(Request $request)
    {

        // return $request;

        $saveQuery = new Estrategia();
        $saveQuery->query = $request->query_text;
        $saveQuery->onlyWhere = $request->onlyWhere;
        $saveQuery->channels = $request->channels;
        $saveQuery->table_name = $request->table_name;
        $saveQuery->query_description = $request->query_description;
        $saveQuery->prefix_client = $request->prefix;
        $saveQuery->repeatUsers = $request->repeatUsers;

        // return $saveQuery;


        $saveQuery->save();


        if ($request->location == 'create') {
            return redirect()->route('estrategia.index');
        } else {
            return back();
        }
    }

    public function show($id)
    {

        $data = Estrategia::find($id);

        $config_layout = [
            'title-section' => 'Estrategia: ' . $data->query_description,
            'breads' => 'Estrategias > Estrategia: ' . $data->query_description,
            'btn-back' => 'estrategia.index'
        ];


        return view('estrategias/show', compact('data', 'config_layout'));


        // $response = Http::get(env('API_URL').self::PATH_API.'/'.$id);
        // $data = $response->json();

        // return view('estrategias/show', compact('data'));

        //return $data;
    }


    public function runQuery(Request $request)
    {

        // return $request; //Verificar que datos llegan. 

        // Aqui se prueba la consulta y se pueden ver los resultados segun el filtro puesto
        $result = \DB::select($request['query']);

        // Extraemos los nombres de las columnas segun la el cliente que se vaya a utilizar        
        $table = Estructura::select('COLUMN_NAME')->where('TABLE_NAME', '=', $request['table_name'])->get();



        if (count($result) > 0) {

            // se cuenta el total de hallados en la consulta anterior
            $counter = \DB::select("select count(*) as counter from " . $request['table_name'] . " where " . $request['where']);

            //Calculos de factibilidad dependiendo del canal *PREGUNTAR ESTE CASO* 
            switch ($request['channel']) {
                case 1:
                    $factibilidad = \DB::select("select count(movil1) as cc from " . $request['table_name'] . " where " . $request['where'] . " and LENGTH(movil1) != 0");
                    break;
                case 2:
                    $factibilidad = \DB::select("select count(fijo1) as cc from " . $request['table_name'] . " where " . $request['where'] . " and LENGTH(fijo1) != 0");
                    break;
                case 3:
                    $factibilidad = \DB::select("select count(email1) as cc from " . $request['table_name'] . " where " . $request['where'] . " and LENGTH(email1) != 0");
                    break;
            }

            //En base al contador total se le resta el contador segun el canal / (cc = counter channels)
            $resto = $counter[0]->counter - $factibilidad[0]->cc;
            // Se calcula el procentaje y se formatea
            // number_format(int or float, numero de decimales, coma para los decimales, punto para los miles)
            $porcentaje = number_format(($counter[0]->counter / 10000) * 100, 2, ',', '.');

            return [
                'contador' => $counter[0]->counter,  // Muestro el contador
                'result' => $result, // El resultado de la consulta 
                'table' => $table, // Los nombres de las columnas
                'resto' => $resto, // la resta entre la factibilidad y el contador
                'porcentaje' => $porcentaje //el procentaje de factibilidad
            ];
        } else {
            return [
                'result' => 0, // El resultado de la consulta 
                'message' => 'No hay nada que mostrar'
            ];
        }

        return $result;
    }


    public function isActive(Request $request)
    {

        $dataCompare = Estrategia::where('isActive', '=', 1)->get();
        $data = Estrategia::where('id', '=', $request->id)->update(['isActive' => 1, 'type' => 2]);
        return $data;
    }

    public function queryResults($strings_query)
    {

        $query_ruts = [];

        foreach ($strings_query as $v) {
            $query_ruts[] = \DB::table($v[0])->whereRaw($v[1])->pluck('rut')->toArray();
        }

        $results = [];

        for ($i = 0; $i < count($query_ruts); $i++) {
            $arr_compare = $query_ruts[$i];
            $arrs = array_slice($query_ruts, 0, $i);
            $diff = array_diff($arr_compare, ...$arrs );

            $results[] = [
                //'arr' => $i,
                $diff,
                //'total_r' => count($query_ruts[$i]),
            ];
        }

        return $results;
    }


    public function deleteStrategy($id)
    {

        $estrategia = Estrategia::find($id)->delete();
        return back();
    }

    public function acceptedStrategy(Request $request)
    {





        /** 
         * CASO PARA SI SOLO ES UNO Y YAAAAA
         */

        //  $getStrategys = Estrategia::select('channels')
        // ->where('isActive', '=', 1)
        // ->where('type', '=', 2)
        // ->get();

        // $estrategia = Estrategia::find($request->id);

        // $a = [];
        // foreach( $getStrategys as  $v){
        //     $a[] = $v->channels;

        // }

        // if(in_array($estrategia->channels, $a)){
        //     return [
        //         'message' => 'No se puede registrar, para ese canal ya existe una estrategia y no se pueden activar mas',
        //         'result' => 0
        //     ];
        //  }else{
        //      $estrategia->type = 2;
        //      $estrategia->isActive = 1;

        //      $estrategia->activation_date = date('Y-m-d');
        //      $estrategia->activation_time = date('G:i:s');

        //      $estrategia->save();
        //      return ['message' => 'Puesto en produccion', 'result' => 1];
        //  }


        /** 
         * CASO PARA SI SOLO LOS MULTIPLES
         */

        $getStrategys = Estrategia::select('channels')
            ->where('isActive', '=', 1)
            ->where('type', '=', 2)
            ->get();

            

        $estrategia = Estrategia::find($request->id); // obtengo los dato de la estrategia segun el identificador


        // return $estrategia;

        //Obtengo la configuracion de los permisos de los canales del cliente.
        $client = DB::table('clients')
                        ->select('active_channels')
                        ->where('prefix', '=', $estrategia->prefix_client)->get()[0];
                    

        //guardo en un array los permisos del cliente 
        $permitidos_client = json_decode($client->active_channels, true);


        // return $permitidos_client[0]['multiple'];

        $arr_key_permitidos = [];

        foreach ($permitidos_client as $k => $v) {
            if (isset($permitidos_client[$k]['multiple'])) { // Verifico y almaceno la posicion de los canales en los cuales se permite usar varias veces el mismo canal
                $arr_key_permitidos[] = $k;
            }
        }

        // return $arr_key_permitidos;

        $arr = [];
        foreach ($getStrategys as  $v) { // Almaceno los canales que existen actualmente para el cliente
            $arr[] = $v->channels;
        }

        // return $arr;

        if (in_array($estrategia->channels, $arr)) { // Verifico si existe ese canal dentro de los registros que existen
            if (in_array($estrategia->channels, $arr_key_permitidos)) { // Verifico si ese canal se puede usar multiple veces para el caso positivo, lo paso a prodccion
                $estrategia->type = 2;
                $estrategia->isActive = 1;
                $estrategia->activation_date = date('Y-m-d');
                $estrategia->activation_time = date('G:i:s');
                $estrategia->save();
                return ['message' => 'Puesto en produccion', 'result' => 1];
            } else { // Para el caso negativo donde no se puedan registrar multiples mensajes, le aviso al usuario
                return [
                    'message' => 'No se puede registrar, para ese canal ya existe una estrategia y no se pueden activar mas',
                    'result' => 0
                ];
            }
        } else { // El caso negativo d que el canal no se encuentre dentro de los registros actuales 
            $estrategia->type = 2;
            $estrategia->isActive = 1;
            $estrategia->activation_date = date('Y-m-d');
            $estrategia->activation_time = date('G:i:s');
            $estrategia->save();
            return ['message' => 'Puesto en produccion', 'result' => 1];
        }
    }

    public function stopStrategy($id)
    {
        $estrategia = Estrategia::find($id);
        $estrategia->isDelete = 1;
        $estrategia->isActive = 0;
        $estrategia->save();
        return back();
    }
}
