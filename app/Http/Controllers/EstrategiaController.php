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
        $this->middleware('permission:root-create|strategy-create', ['only' => ['create', 'store', 'saveEstrategia', 'isActive', 'acceptedStrategy']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update']]);
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

        $channels = DB::table('canales')->where('isActive', '=', 1)->pluck('name')->toArray();

        foreach ($data as  $d) {
            if (isset($channels[$d->channels])) {
                $d->canal = $channels[$d->channels];
            }
        }

        // return $data;
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
        $saveQuery->onlyWhere = $request->onlyWhere;
        $saveQuery->channels = $request->channels;
        $saveQuery->table_name = $request->table_name;
        $saveQuery->prefix_client = $request->prefix;

        $saveQuery->registros_unicos = $request->unic;
        $saveQuery->registros_repetidos = $request->repe;
        $saveQuery->total_registros = $request->total;
        $saveQuery->cobertura = $request->cober;
        $saveQuery->type = 1;

        $saveQuery->registros = json_encode(json_decode($request['registros'], true));


        if (isset($request->repeatUsers)) {
            $saveQuery->repeatUsers = $request->repeatUsers;
        } else {
            $saveQuery->repeatUsers = 0;
        }

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


    public function isActive(Request $request)
    {

        $dataCompare = Estrategia::where('isActive', '=', 1)->get();



        $data = Estrategia::where('id', '=', $request->id)->update(['isActive' => 1, 'type' => 2]);
        return $data;
    }



    public function probarStrategy(Request $request)
    {



        $datas = DB::table('estrategias')
            ->select('id', 'onlyWhere', 'table_name', 'channels', 'isActive', 'isDelete', 'type', 'repeatUsers', 'registros')
            ->where('prefix_client', '=', $request->prefix)
            ->whereIn('isActive', [0, 1])
            ->whereIn('type', [0, 1, 2])
            ->where('isDelete', '=', 0)
            ->orderBy('isActive', 'DESC')
            ->orderBy('type', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->get();


        $queries = [];

        foreach ($datas as $key => $val) {
            $queries[$key][] = $val->table_name;
            $queries[$key][] = $val->onlyWhere;
            $queries[$key][] = $val->type;
            $queries[$key]['registros'] = json_decode($val->registros, true);
            $queries[$key]['total_cartera'] = 10000;
        }

        $data_counter = count($datas);

        $queries[$data_counter][] = $request['table_name'];
        $queries[$data_counter][] = $request['query'];
        $queries[$data_counter][] = 0;
        $queries[$data_counter]['total_cartera'] = 10000;


        // return $queries;


        return $this->queryResults($queries);
    }

    public function queryResults($strings_query)
    {
        $query_ruts = [];
        $query_ruts_existe = [];
        $results = [];



        // return $strings_query;






        foreach ($strings_query as $value) {

            if ($value[2] === 2 || $value[2] === 1) {
                $query_ruts[] = $value['registros'];
            } else {
                $query_ruts[] = DB::table($value[0])->whereRaw($value[1])->pluck('rut')->toArray();
            }
        }


        // return $strings_query[1][2];


        $array = array();
        for ($i = 0; $i < count($query_ruts); $i++) {

            if ($strings_query[$i][2] === 1 || $strings_query[$i][2] === 2) {
                $array[$i]['unicos'] = $query_ruts[$i];
                $array[$i]['repetidos'] = 0;
            } else {
                $tempArr = [];
                for ($j = 0; $j < count($query_ruts); $j++) {
                    if ($i !== $j) {
                        $tempArr = array_merge($tempArr, $query_ruts[$j]);
                    }
                }

                $merge[$i] = array_unique(array_merge($tempArr));

                $array[$i]['unicos'] = array_filter($query_ruts[$i], function ($valor) use ($merge, $i) {
                    return !in_array($valor, $merge[$i]);
                });

                $array[$i]['repetidos'] = array_filter($query_ruts[$i], function ($valor) use ($merge, $i) {
                    return in_array($valor, $merge[$i]);
                });
            }

            $total_unicos[$i] = count($array[$i]['unicos']);

            if($array[$i]['repetidos'] != 0){
                $total_repetidos[$i] = count($array[$i]['repetidos']);
            }else{
                $total_repetidos[$i] = $array[$i]['repetidos'];
            }
            

            $percent_cober[$i] = ($total_unicos[$i]/10000)*100;
            $total_r[$i] = count($query_ruts[$i]);

            $results[$i] = [
                        'unicos' => array_values($array[$i]['unicos']),
                        'total_unicos' => $total_unicos[$i],
                        'total_repetidos' => $total_repetidos[$i],
                        'percent_cober' => $percent_cober[$i],
                        'total_r' => $total_r[$i],
                    ];



        }

        return $results;









        // for ($i = 0; $i < count($query_ruts); $i++) {
        //     $tempArr = []; 
        //     for ($j = 0; $j < count($query_ruts); $j++) {
        //         if ($i !== $j) {
        //             $tempArr = array_merge($tempArr, $query_ruts[$j]);
        //         }
        //     }

        //     $merge[$i] = array_unique(array_merge($tempArr));

        //     $unicos[] = array_diff($query_ruts[$i], $merge[$i]);

        //     $repetidos[] = array_intersect($query_ruts[$i], $merge[$i]);

        //     $total_unicos[] = count(array_diff($query_ruts[$i], $merge[$i]));

        //     $total_repetidos[] = count(array_intersect($query_ruts[$i], $merge[$i]));

        //     $percent_cober[] = (count(array_diff($query_ruts[$i], $merge[$i])) / $strings_query[$i]['total_cartera']) * 100;

        //     $total_r[] = count($query_ruts[$i]);

        //     $results = [
        //         'unicos' => $unicos,
        //         'total_unicos' => $total_unicos,
        //         'total_repetidos' => $total_repetidos,
        //         'percent_cober' => $percent_cober,
        //         'total_r' => $total_r,
        //         'criterio' => $strings_query[$i]
        //     ];
        // }
        // Realizamos el retorno.
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
        $estrategia->type = 3;
        $estrategia->save();
        return back();
    }
}
