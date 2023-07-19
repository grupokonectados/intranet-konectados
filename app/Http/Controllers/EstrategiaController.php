<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use App\Models\Estructura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EstrategiaController extends Controller
{


    function __construct()
    {
        $this->middleware('permission:root-list|strategy-list', ['only' => ['index', 'show', 'queryResults', 'probarStrategy']]);
        $this->middleware('permission:root-create|strategy-create', ['only' => ['create', 'store', 'saveEstrategia', 'isActive', 'acceptedStrategy']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:root-delete|strategy-delete', ['only' => ['destroy', 'deleteStrategy', 'stopStrategy']]);
    }

    const PATH_API = '/estrategias';


    public function saveEstrategia(Request $request)
    {

        $getEstrategiasCliente = Http::get(env('API_URL').'/estrategias/diseno/'.$request->prefix);
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
                'message' => 'Error! existe un criterio creado el dia de hoy para ese canal, con las mismas caracteristicas. Por favor, verifíquelo e inténtelo nuevamente.',
            ];
            return back()->with('message', $message);
        }else{
            $onlyWhere = $request->onlyWhere;

            $saveQuery = new Estrategia();
            $saveQuery->onlyWhere = str_replace("'","''", $onlyWhere);
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


            // return $saveQuery;
    
            $save = Http::post(env('API_URL').env('API_ESTRATEGIA'), $saveQuery);
            $result = $save->json();

            if($result != false){
                if($result['protocol41'] === true){
                    $message = [
                        'type' => 'success',
                        'message' => 'Exito! Se registro la estrategia con exito',
                    ];
                    return back()->with('message', $message);
                }else{
                    $message = [
                        'type' => 'danger',
                        'message' => 'Error! Hubo un problema y su estrategia no se registro correctamente. Por favor, verifíquelo e inténtelo nuevamente.',
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

        // return ;

        $query_ruts = [];


        $estrategias_cache = Cache::get('estrategias');


        // return $estrategias_cache;


        $param = [
            "idCliente" =>$request->id_cliente,
            "cartera"=> $request->table_name,
            "criterio"=> $request['query'],
        ];

       

        $result_query = Http::withBody(json_encode($param))->get("http://apiest.konecsys.com:8080/estrategia/records");
        
        $coleccion = $result_query->collect()[0];


        // return $coleccion;
        $response_ruts = array_values(json_decode($coleccion[0]['detail_records'], true));

        $full_merge = [];

        for($i = 0; $i<count($estrategias_cache); $i++){
 
                    $full_merge = array_merge($full_merge, json_decode($estrategias_cache[$i]['registros'],true));
                    $xd[] = $i;            
        }
        // return count($full_merge);


        $unicos = array_diff($response_ruts, $full_merge);
        $iguales = array_intersect($response_ruts, $full_merge);


        if(isset($request->check)){
            $cobertura = ( $coleccion[0]['total_records'] / $coleccion[0]['total_cartera'])*100;
        }else{
            $cobertura = ( count($unicos) / $coleccion[0]['total_cartera'])*100;
        }


        $result = [
            'unicos' => $unicos,
            'total_unicos' => count($unicos),
            'total_repetidos' => count($iguales),
            'total_r' => $coleccion[0]['total_records'],
            'percent_cober' => $cobertura,
            'total_enc' => $response_ruts
        ];


        return $result;
/*
        for($r = 0; $r <=count($estrategias_cache); $r++){
            if($r != count($estrategias_cache)){
                if($estrategias_cache[$r]['type'] === 1 || $estrategias_cache[$r]['type'] === 2){
                    $query_ruts[] = json_decode($estrategias_cache[$r]['registros'], true);
                }
            }else{
                $query_ruts[] = $response_ruts;
                for ($j = 0; $j < count($query_ruts); $j++) {
                    if ($r !== $j) {
                        $tempArr = array_merge($tempArr, $query_ruts[$j]);
                    }
                    $merge[$r] = array_unique(array_merge($tempArr));
                }
                $arr[$r]['unicos'] = array_filter($query_ruts[$r], function ($valor) use ($merge, $r) { return !in_array($valor, $merge[$r]); });
                $arr[$r]['iguales'] = array_filter($query_ruts[$r], function ($valor) use ($merge, $r) { return in_array($valor, $merge[$r]); });

                $result[$r] = [
                    'unicos' => $arr[$r]['unicos'],
                    'repetidos' => count($arr[$r]['iguales']),
                    'total_unicos' => count($arr[$r]['unicos']),
                    'total_repetidos' => count($arr[$r]['iguales']),
                    'total_r' => $coleccion[0]['total_records'],
                    'percent_cober' => ( $coleccion[0]['total_records']/ $coleccion[0]['total_cartera'])*100
                ];
            }
            
        }


        return $result;

        $tempArr = [];
        
        $arr= [];
        



        for($i = 0; $i <count($query_ruts); $i++){
            if(count($query_ruts) < $i){
                if ($estrategias_cache[$i]['type'] === 1 || $estrategias_cache[$i]['type'] === 2) {
                    $arr[$i]['unicos'] = $query_ruts[$i];
                    $arr[$i]['repetidos'] = 0;
                    $xd[]['vuelta'] = $i;
                } else {
                    for ($j = 0; $j < count($query_ruts); $j++) {
                        if ($i !== $j) {
                            $tempArr = array_merge($tempArr, $query_ruts[$j]);
                        }
                    }
                    $merge[$i] = array_unique(array_merge($tempArr));
                    $xd[]['vuelta'] = $i;
                }
            }
        }

        return $xd;

       */

    }

    public function queryResults($strings_query)
    {
        $query_ruts = [];
        $results = [];
        $arr = [];


        // $param =
        // [
        
        //     "idCliente" =>11,
        //     "cartera"=> "cartera_primer_dia",
        //     "criterio"=> "monto <= 2000 and monto >=1000"
        // ];
        //     $ruts = Http::withBody(json_encode($param))->
        //     get("http://apiest.konecsys.com:8080/estrategia/records");


        

        foreach ($strings_query as $value) {
            if ($value[2] === 2 || $value[2] === 1) {
                $query_ruts[] = $value['registros'];
            } else {
                $query_ruts[] = DB::table($value[0])->whereRaw($value[1])->pluck('rut')->toArray(); //id/criterio/table_name
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

    public function acceptedStrategy(Request $request)
    {



        // return $request;

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

        

        $getEstrategiasCliente = Http::get(env('API_URL').'/estrategias/diseno/'.$request->id);

        return $getEstrategiasCliente->collect()[0];

        $arr_key_permitidos = [];

        //Esperar el enpoint para activar la estrategia.

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
