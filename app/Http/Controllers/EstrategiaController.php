<?php

namespace App\Http\Controllers;

use App\Models\Estrategia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EstrategiaController extends Controller
{


    function __construct()
    {
        $this->middleware('permission:root-list|strategy-list', ['only' => ['probarStrategy', 'show']]);
        $this->middleware('permission:root-create|strategy-create', ['only' => ['saveEstrategia', 'acceptedStrategy']]);
    }




    public function saveEstrategia(Request $request)
    {

        // return $request;
        $getEstrategiasCliente = Http::get(env('API_URL') . env('API_ESTRATEGIAS') . '/diseno/' . $request->prefix);

        $data = $getEstrategiasCliente->collect()[0];
        $exist_record = [];
        foreach ($data as $key => $value) {
            if ($value['onlyWhere'] === $request->onlyWhere) {
                if ($value['channels'] === (int)$request->channels) {
                    if (date('Y-m-d', strtotime($data[$key]['created_at'])) == date('Y-m-d')) {
                        $exist_record[] = 1;
                    }
                }
            }
        }

        if (count($exist_record) > 0) {
            $message = [
                'type' => 'danger',
                'message' => 'Error! existe un criterio creado el dia de hoy para ese canal, con las mismas caracteristicas. Por favor, verifíquelo e inténtelo nuevamente.',
            ];
            return back()->with('message', $message);
        } else {
            $onlyWhere = $request->onlyWhere;

            $saveQuery = [];
            $saveQuery['onlyWhere'] = str_replace("'", "''", $onlyWhere);
            $saveQuery['channels'] = $request->channels;
            $saveQuery['table_name'] = $request->table_name;
            $saveQuery['prefix_client'] = $request->prefix;
            $saveQuery['registros_unicos'] = $request->unic;
            $saveQuery['registros_repetidos'] = $request->repe;
            $saveQuery['total_registros'] = $request->total;
            $saveQuery['cobertura'] = $request->cober;
            $saveQuery['type'] = 1;

            $saveQuery['registros'] = json_encode(json_decode($request['registros'], true));

            if (isset($request->template)) {
                $saveQuery['idEmailTemplate'] = (int) $request->template;
            } else {
                $saveQuery['idEmailTemplate'] = 0;
            }

            $save = Http::post(env('API_URL') . env('API_ESTRATEGIA'), $saveQuery);
            $result = $save->json();



            if ($result != false) {
                if ($result['serverStatus'] === 2) {
                    $message = [
                        'type' => 'success',
                        'message' => 'Exito! Se registro la estrategia con exito',
                    ];
                    return back()->with('message', $message);
                } else {
                    $message = [
                        'type' => 'danger',
                        'message' => 'Error! Hubo un problema y su estrategia no se registro correctamente. Por favor, verifíquelo e inténtelo nuevamente.',
                    ];
                    return back()->with('message', $message);
                }
            } else {
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
        $config_channels = Cache::get('config_channels');
        $tipos_masivos = [];

        foreach ($config_channels['channels'] as $o => $value) {
            if (isset($value['tipo'])) {
                $tipos_masivos[$o] = $o;
            }
        }

        $param = [
            "idCliente" => $request->id_cliente,
            "cartera" => $request->table_name,
            "criterio" => $request['query'],
            "template" => $request->template,
            'canal' => $request->channel,
        ];



        $result_query = Http::withBody(json_encode($param))->get(env('API_URL') . env('API_ESTRATEGIA') . "/records");

        // try {
        //     $coleccion = $result_query->collect()[0];
        //     if ($coleccion[0]['total_records'] !== 0) {
        //         $response_ruts = array_values(json_decode($coleccion[0]['detail_records'], true));
        //     }
        // } catch (\Throwable $th) {
        //     return response()->json(['error' => 'Error en la consulta'], 404);
        // }

        $coleccion = $result_query->collect()[0];
        // return $coleccion[0]['total_records'];
        if ($coleccion[0]['total_records'] !== 0) {
            $response_ruts = array_values(json_decode($coleccion[0]['detail_records'], true));
        } else {
            return response()->json(['error' => 'Error en la consulta'], 404);
        }

        // return $response_ruts;

        $full_merge = [];

        if (in_array($request->channel, $tipos_masivos)) {
            for ($i = 0; $i < count($estrategias_cache); $i++) {
                if (in_array($estrategias_cache[$i]['channels'], $tipos_masivos)) {
                    $full_merge = array_merge($full_merge, json_decode($estrategias_cache[$i]['registros'], true));
                }
            }

            $unicos = array_diff($response_ruts, $full_merge);
            $iguales = array_intersect($response_ruts, $full_merge);

            if (isset($request->check)) {
                $cobertura = ($coleccion[0]['total_records'] / $coleccion[0]['total_cartera']) * 100;
            } else {
                $cobertura = (count($unicos) / $coleccion[0]['total_cartera']) * 100;
            }

            return $result = [
                'unicos' => array_values($unicos),
                'total_unicos' => count($unicos),
                'total_repetidos' => count($iguales),
                'total_r' => $coleccion[0]['total_records'],
                'percent_cober' => $cobertura,
                'total_enc' => $response_ruts,
            ];
        } else {
            for ($i = 0; $i < count($estrategias_cache); $i++) {
                if (!in_array($estrategias_cache[$i]['channels'], $tipos_masivos)) {
                    $full_merge = array_merge($full_merge, json_decode($estrategias_cache[$i]['registros'], true));
                }
            }

            $unicos = array_diff($response_ruts, $full_merge);
            $iguales = array_intersect($response_ruts, $full_merge);

            if (isset($request->check)) {
                $cobertura = ($coleccion[0]['total_records'] / $coleccion[0]['total_cartera']) * 100;
            } else {
                $cobertura = (count($unicos) / $coleccion[0]['total_cartera']) * 100;
            }

            return $result = [
                'unicos' => array_values($unicos),
                'total_unicos' => count($unicos),
                'total_repetidos' => count($iguales),
                'total_r' => $coleccion[0]['total_records'],
                'percent_cober' => $cobertura,
                'total_enc' => $response_ruts,
            ];
        }
    }


    public function show($id)
    {
    }

    public function acceptedStrategy(Request $request)
    {

        // return $request;

        // $estrategia = Cache::get('estrategias');
        // $permitidos_client = Cache::get('config_channels');

        // $param = [
        //     'prefix' => $estrategia[0]['prefix_client'],
        //     'type' => 2
        // ];


        // $estrategiasHistoricoCliente = Http::withBody(json_encode($param))->get(env('API_URL').env('API_ESTRATEGIA').'/tipo');
        // $datas = $estrategiasHistoricoCliente->collect()[0];


        // foreach($estrategia as $value){
        //     if(in_array($request->id, $value)){
        //         $arr_estrategia = $value;
        //     }
        // }


        // $arr_key_permitidos = [];


        // foreach ($permitidos_client as $k => $v) {
        //     if (isset($permitidos_client[$k]['multiple'])) { // Verifico y almaceno la posicion de los canales en los cuales se permite usar varias veces el mismo canal
        //         $arr_key_permitidos[] = $k;
        //     }
        // }


        // $arr = [];
        // foreach ($datas as  $v) { // Almaceno los canales que existen actualmente para el cliente
        //     $arr[] = $v['channels'];
        // }

        // // return ($arr);

        // if (in_array($arr_estrategia['channels'], $arr)) { // Verifico si existe ese canal dentro de los registros que existen
        //     if (in_array($arr_estrategia['channels'], $arr_key_permitidos)) { // Verifico si ese canal se puede usar multiple veces para el caso positivo, lo paso a prodccion

        //     } else { // Para el caso negativo donde no se puedan registrar multiples mensajes, le aviso al usuario
        //         return [
        //             'message' => 'No se puede registrar, para ese canal ya existe una estrategia y no se pueden activar mas',
        //             'result' => 0
        //         ];
        //     }
        // } else { // El caso negativo d que el canal no se encuentre dentro de los registros actuales 
        //     $actived = Http::put("http://apiest.konecsys.com:8080/estrategia/activar/".$arr_estrategia['id']);

        //     return ['message' => 'Puesto en produccion', 'result' => $actived['status']];
        // }

        $actived = Http::put("http://apiest.konecsys.com:8080/estrategia/activar/" . $request->id);
        return ['message' => 'Puesto en produccion', 'result' => $actived['status']];


        switch ($request->channels) {
            case '1':
                $arr = [
                    'prefix' => $request->prefix,
                    ['1465254-K', 'RUY ENRIQUE BARBOSA', 'jehfebles@gmail.com'],
                    ['1511211-5', 'LUIS MARCELINO MORENO SÁNCHEZ', 'jehefebles@gmail.com'],
                    ['1702532-5', 'JOSE SANTOS MALDONADO TAPIA', 'jehfebles1@gmail.com'],
                    ['1705969-6', 'AIDA CACERES', 'jesus@grupokonectados.cl']
                ];

                return (new MailNotifyController)->send_notify($arr);
                break;

            default:

                $actived = Http::put(env('API_URL') . env('API_ESTRATEGIA') . "/activar/" . $request->id);
                break;
        }

        return ['message' => 'Puesto en produccion', 'result' => $actived['status']];
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
            $estrategiasHistoricoCliente = Http::withBody(json_encode($param))->get(env('API_URL') . env('API_ESTRATEGIA') . '/historico');
            $datas = $estrategiasHistoricoCliente->collect()[0];
        } else {
            $param = [
                'prefix' => $request->client,
                'type' => 3
            ];

            $estrategiasHistoricoCliente = Http::withBody(json_encode($param))->get(env('API_URL') . env('API_ESTRATEGIA') . '/tipo');
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
