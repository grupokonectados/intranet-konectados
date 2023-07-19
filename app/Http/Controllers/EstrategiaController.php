<?php

namespace App\Http\Controllers;

use App\Models\Estrategia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EstrategiaController extends Controller
{


    function __construct()
    {
        $this->middleware('permission:root-list|strategy-list', ['only' => ['index', 'show', 'queryResults', 'probarStrategy']]);
        $this->middleware('permission:root-create|strategy-create', ['only' => ['create', 'store', 'saveEstrategia', 'acceptedStrategy']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:root-delete|strategy-delete', ['only' => ['destroy']]);
    }

    public function saveEstrategia(Request $request)
    {

        $getEstrategiasCliente = Http::get(env('API_URL').env('API_ESTRATEGIA').'/diseno/'.$request->prefix);
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

            $save = Http::post(env('API_URL').env('API_ESTRATEGIAS'), $saveQuery);
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
                    'message' => 'Error! Alguno de los campos de la estrategia no estan correctamente llenado. Por favor, verifíquelo e inténtelo nuevamente.',
                ];
                return back()->with('message', $message);
            }
        }
    }

    public function probarStrategy(Request $request)
    {
        $estrategias_cache = Cache::get('estrategias');

        $param = [
            "idCliente" =>$request->id_cliente,
            "cartera"=> $request->table_name,
            "criterio"=> $request['query'],
        ];

        $result_query = Http::withBody(json_encode($param))->get(env('API_URL').env('API_ESTRATEGIA')."/records");
        $coleccion = $result_query->collect()[0];
        $response_ruts = array_values(json_decode($coleccion[0]['detail_records'], true));

        $full_merge = [];

        for($i = 0; $i<count($estrategias_cache); $i++){
            $full_merge = array_merge($full_merge, json_decode($estrategias_cache[$i]['registros'],true));        
        }

        $unicos = array_diff($response_ruts, $full_merge);
        $iguales = array_intersect($response_ruts, $full_merge);


        if(isset($request->check)){
            $cobertura = ($coleccion[0]['total_records'] / $coleccion[0]['total_cartera'])*100;
        }else{
            $cobertura = (count($unicos) / $coleccion[0]['total_cartera'])*100;
        }

        return $result = [
            'unicos' => $unicos,
            'total_unicos' => count($unicos),
            'total_repetidos' => count($iguales),
            'total_r' => $coleccion[0]['total_records'],
            'percent_cober' => $cobertura,
            'total_enc' => $response_ruts
        ];

    }

    

    public function acceptedStrategy(Request $request)
    {


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

    // Filtro 

    public function filterStrategy(Request $request)
    {      
        // return $request;

        if ($request->canal !== 'refresh') {
            $param = [
                'prefix' => $request->client,
                'canal' => $request->canal
            ];
            $estrategiasHistoricoCliente = Http::withBody(json_encode($param))->get(env('API_URL').env('API_ESTRATEGIA').'/historico');
            $datas = $estrategiasHistoricoCliente->collect()[0];
        } else {
            $param = [
                'prefix' => $request->client,
                'type' => 3
            ];

            $estrategiasHistoricoCliente = Http::withBody(json_encode($param))->get(env('API_URL').env('API_ESTRATEGIA').'/tipo');
            $datas = $estrategiasHistoricoCliente->collect()[0];
        }

        $channels = Cache::get('canales');
        foreach ($datas as $key => $data) {
            if (in_array($data['channels'], array_keys($channels))) {
                $datas[$key]['canal'] = strtoupper($channels[$data['channels']]['name']);
            }
        }

        

        return $datas;
    }
}
