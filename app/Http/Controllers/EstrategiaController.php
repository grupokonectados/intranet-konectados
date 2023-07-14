<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use App\Models\Estructura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

        return view('estrategias/index', compact('config_layout'));
    }


    public function saveEstrategia(Request $request)
    {

        $getEstrategiasCliente = Http::get(env('API_URL').env('API_ESTRATEGIA').'/'.$request->prefix);
        $data = $getEstrategiasCliente->collect()[0];

        $exist_record = [];

        foreach ($data as $key => $value) {
            if ($value['onlyWhere'] === $request->onlyWhere) {
                if($value['channels'] === (int)$request->channels){
                    if(date('Y-m-d', strtotime($data[$key]['created_at'])) == date('Y-m-d')){
                        $exist_record[] = 1;
                    }
                }
            } 
        }

        if(count($exist_record) > 0){
            $message = [
                'type' => 'danger',
                'message' => 'Error!, existe un criterio creado el dia de hoy para ese canal, con las mismas caracteristicas. Por favor, verifíquelo e inténtelo nuevamente.',
            ];
            return back()->with('message', $message);
        }else{

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
    
            if (isset($request->repeatUsers)) {
                $saveQuery->repeatUsers = $request->repeatUsers;
                $saveQuery->registros = json_encode(json_decode($request['registros'], true));
            } else {
                $saveQuery->repeatUsers = 0;
                $saveQuery->registros = json_encode(json_decode($request['registros'], true));
            }
    
            $save = Http::post(env('API_URL').env('API_ESTRATEGIA'), $saveQuery);
            $result = $save->json();

            if($result != false){
                if($result['protocol41'] === true){
                    $message = [
                        'type' => 'success',
                        'message' => 'Exito!, Se registro la estrategia con exito',
                    ];
                    return back()->with('message', $message);
                }else{
                    $message = [
                        'type' => 'danger',
                        'message' => 'Error!, Hubo un problema y su estrategia no se registro correctamente. Por favor, verifíquelo e inténtelo nuevamente.',
                    ];
                    return back()->with('message', $message);
                }
            }else{
                $message = [
                    'type' => 'danger',
                    'message' => 'Error!, Alguno de los campos de la estrategia no estan correctamente llenado. Por favor, verifíquelo e inténtelo nuevamente.',
                ];
                return back()->with('message', $message);
            }
        }
    }

    public function probarStrategy(Request $request)
    {

        return $request;
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
            $queries[$key][] = $val->repeatUsers;

            $queries[$key]['registros'] = json_decode($val->registros, true);
            $queries[$key]['total_cartera'] = 10000;
        }

        $data_counter = count($datas);

        $queries[$data_counter][] = $request['table_name'];
        $queries[$data_counter][] = $request['query'];
        $queries[$data_counter][] = 0;
        $queries[$data_counter][] = intval($request->check);
        $queries[$data_counter]['total_cartera'] = 10000;

        return $this->queryResults($queries);
    }

    public function queryResults($strings_query)
    {
        $query_ruts = [];
        $results = [];
        $arr = [];

        foreach ($strings_query as $value) {
            if ($value[2] === 2 || $value[2] === 1) {
                $query_ruts[] = $value['registros'];
            } else {
                $query_ruts[] = DB::table($value[0])->whereRaw($value[1])->pluck('rut')->toArray();
            }
        }


        for ($i = 0; $i < count($query_ruts); $i++) {

            if ($strings_query[$i][2] === 1 || $strings_query[$i][2] === 2) {
                $arr[$i]['unicos'] = $query_ruts[$i];
                $arr[$i]['repetidos'] = 0;
            } else {
                $tempArr = [];
                for ($j = 0; $j < count($query_ruts); $j++) {
                    if ($i !== $j) {
                        $tempArr = array_merge($tempArr, $query_ruts[$j]);
                    }
                }

                $merge[$i] = array_unique(array_merge($tempArr));

                $arr[$i]['unicos'] = array_filter($query_ruts[$i], function ($valor) use ($merge, $i) {
                    return !in_array($valor, $merge[$i]);
                });

                $arr[$i]['repetidos'] = array_filter($query_ruts[$i], function ($valor) use ($merge, $i) {
                    return in_array($valor, $merge[$i]);
                });
            }

            $total_unicos[$i] = count($arr[$i]['unicos']);

            if ($arr[$i]['repetidos'] != 0) {
                $total_repetidos[$i] = count($arr[$i]['repetidos']);
            } else {
                $total_repetidos[$i] = $arr[$i]['repetidos'];
            }

            if ($strings_query[$i][3] === 0) {
                $percent_cober[$i] = ($total_unicos[$i] / 10000) * 100;
            } else {
                $percent_cober[$i] = (count($query_ruts[$i]) / 10000) * 100;
            }



            // $percent_cober[$i] = ($total_unicos[$i] / 10000) * 100;
            $total_r[$i] = count($query_ruts[$i]);



            $results[$i] = [
                'unicos' => array_values($arr[$i]['unicos']),
                'repetidos' =>  $arr[$i]['repetidos'] === 0 ? 0 : array_values($arr[$i]['repetidos']),
                'total_unicos' => $total_unicos[$i],
                'total_repetidos' => $total_repetidos[$i],
                'percent_cober' => $percent_cober[$i],
                'total_r' => $total_r[$i],
                'total_enc' => $query_ruts[$i],
            ];
        }


        // return $arr;

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

        Estrategia::find($id)->delete();
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




    // Filtro 

    public function filterStrategy(Request $request)
    {



        if ($request->canal !== 'refresh') {
            $datas = DB::table('estrategias')
                ->select('id', 'onlyWhere', 'channels', 'activation_date', 'activation_time',)
                ->where('channels', '=', $request->canal)
                ->where('isDelete', '=', 1)
                ->orderBy('activation_date', 'DESC')
                ->orderBy('activation_time', 'DESC')
                ->get();
        } else {
            $datas = DB::table('estrategias')
                ->select('id', 'onlyWhere', 'channels', 'activation_date', 'activation_time',)
                ->where('isDelete', '=', 1)
                ->orderBy('activation_date', 'DESC')
                ->orderBy('activation_time', 'DESC')
                ->get();
        }


        $channels = DB::table('canales')->where('isActive', '=', 1)->pluck('name')->toArray();

        foreach ($datas as $key => $data) {
            if (isset($channels[$data->channels])) {
                $data->canal = $channels[$data->channels];
            }
        }

        return $datas;
    }
}
