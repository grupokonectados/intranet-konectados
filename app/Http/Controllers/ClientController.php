<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use App\Models\Estructura;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Ui\Presets\React;

class ClientController extends Controller
{

    const PATH_API = '/clients';

    public function index()
    {
        /**
         * Metodo laravel
         */

        $data = Client::all();


        /**
         * Metodo API
         */

        // $response = Http::get(env('API_URL').self::PATH_API);
        // $data = $response->json();


        $config_layout = [
            'title-section' => 'Clientes',
            'breads' => 'Clientes',
        ];

        return view('clients/index', compact('data', 'config_layout'));
    }


    public function edit($id)
    {

        $client = Client::find($id);

        $channels = DB::table('canales')->where('isActive', '=', 1)->pluck('name')->toArray();

        $client->active_channels = json_decode($client->active_channels, true);

        $config_layout = [
            'title-section' => 'Editar: ' . $client->name,
            'breads' => 'Clientes > Editar: ' . $client->name,
            'btn-back' => 'clients.show'
        ];

        return view('clients/edit', compact('client', 'config_layout', 'channels'));
    }


    public function update(Request $request, $id)
    {

        //return $request;
        $client = Client::find($id);
        $client->active_channels = json_encode($request['channels']);
        $client->save();

        return redirect(route('clients.show', $id));
    }


    public function searchCliente(Request $request)
    {

        /**
         * Metodo laravel
         */

        $prefix = $request->prefix;
        $query = Estructura::select('COLUMN_NAME', 'COLUMN_TYPE', 'DATA_TYPE', 'TABLE_NAME')->where("PREFIX", '=', $prefix)->get();
        return $query;



        /**
         * Metodo API
         */

        // $body_req = [
        //     'prefix' => $request->prefix,
        // ];

        // $response = Http::post(env('API_URL') . '/estructura76/search-estructura', $body_req);
        // return $response;
    }



    public function disenoEstrategia($id)
    {

        $client = Client::select('id', 'name', 'prefix', 'active_channels')->find($id); //Traigo los datos del cliente

        // convierto en un array los canales permitidos del cliente
        $client->active_channels = json_decode($client->active_channels, true);

        //Extraemos el nombre de los canales
        $channels = DB::table('canales')->where('isActive', '=', 1)->pluck('name')->toArray();

        // Traemo la estructura de la tabla
        $estructura = Estructura::select('COLUMN_NAME', 'COLUMN_TYPE', 'DATA_TYPE', 'TABLE_NAME')->where("PREFIX", '=', $client->prefix)->get();

        //Configuramos la vista
        $config_layout = [
            'title-section' => 'Diseño de estrategia para: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name . ' > Diseño de estrategia',
            'btn-back' => 'clients.show'
        ];

        // Obtengo todos las estrategias que el cliente tiene que no estan activas
        $datas = DB::table('estrategias')
            ->select('id', 'onlyWhere', 'table_name', 'channels', 'isActive', 'isDelete', 'type', 'repeatUsers')
            ->where('prefix_client', '=', $client->prefix)
            ->whereIn('isActive', [0, 1])
            ->whereIn('type', [0, 2])
            ->where('isDelete', '=', 0)
            ->orderBy('isActive', 'DESC')
            ->orderBy('type', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->get();

        $queries = [];
        $ch_approve = [];


        $total_cartera = 0;

        if (count($datas) > 0) { //Verifico que hay datos

            //Tomo el total de la careta de ese cliente
            $total_cartera = DB::select("SELECT COUNT(*) AS total_cartera FROM " . $datas[0]->table_name)[0]->total_cartera;

            // Cargo un arreglo con los valores que utilizare para extraer los registros segun la estrategia
            foreach ($datas as $key => $val) {
                $queries[$key][] = $val->table_name;
                $queries[$key][] = $val->onlyWhere;
            }

            //Realizo la consulta
            $queryResults = (new EstrategiaController)->queryResults($queries);

            // return $queryResults;

            $c = 0;

            // con este recorrido, vamos a asignar algunos valores a el arreglo data por cada resultado individual
            foreach ($datas as $key => $data) {

                // almacenamos los canales que se encuentran en uso.

                $a[] = $data->type;
                if ($data->type === 0) {
                    $canales[] = $data->channels;
                }

                // Verificamos cuales son los que se encuentran en uso, y a la nueva variable canal le asignamos el nombre del canal
                if (isset($channels[$data->channels])) {
                    $data->canal = $channels[$data->channels];
                }

                //Asignamos los registros encontrados. 
                // $data->registros_unicos = $queryResults[$key][0];
                $data->registros_unicos = $queryResults[$c]['unicos'];

                $data->registros_total = $queryResults[$c]['total_r'];

                //Asignamos el conteo total de registros encontrados como unicos
                // $data->total_registros_unicos = count($queryResults[$key][0]);
                $data->total_registros_unicos = count($queryResults[$c]['unicos']);

                //Asignamos el valor del porcentaje en relacion a la cantidad de registros totales. 
                // $data->porcentaje_registros_unicos = (count($queryResults[$key][0]) / $total_cartera) * 100;
                $data->porcentaje_registros_unicos = (count($queryResults[$c]['unicos']) / $total_cartera) * 100;

                if ($c < count($queryResults) - 1) {
                    $data->repetidos = count($queryResults[$c]['total_r']) - count($queryResults[$c + 1]['total_r']);
                }

                if ($c < count($queryResults) - 1) {
                    $c++;
                }






                //Verificamos que los canales activos para el cliente no sean nulos


                if ($client->active_channels != null) {
                    if ($data->type === 0) {
                        // creamos un arreglo con las keys de los canales activos del cliente.
                        $key_active_channels = array_keys($client->active_channels);

                        //Comparamos y creamos un nuevo array con los valores unicos entre las key de los canales activos 
                        // y los canales que se encuentran en uso. 
                        $ch_approve = array_diff($key_active_channels, $canales);
                    } else {
                        $key_active_channels = array_keys($client->active_channels);
                        $ch_approve = $key_active_channels;
                    }
                } else {
                    //En caso que sea nulo, enviamos los arreglos vacios.
                    $ch_approve = [];
                    $client->active_channels = [];
                }
            }

            //En caso de que no exista datos de estrategias
        } else {

            //Verificamos que los canales activos para el cliente no sean nulos
            if ($client->active_channels != null) {
                // creamos un arreglo con las keys de los canales activos del cliente.
                $key_active_channels = array_keys($client->active_channels);

                // Enviamos las esas key
                $ch_approve = $key_active_channels;
            } else {
                //En caso que sea nulo, enviamos los arreglos vacios.
                $ch_approve = [];
                $client->active_channels = [];
            }
        }

        // return $datas;

        return view('clients/diseno', compact('client', 'datas', 'config_layout', 'channels', 'estructura', 'ch_approve'));
    }




    public function show($id, Request $request)
    {

        // return $request;

        /**
         * Metodo laravel
         */

        $client = Client::select('id', 'name', 'prefix', 'active_channels')->find($id); //Traigo los datos del cliente

        // convierto en un array los canales permitidos del cliente
        $client->active_channels = json_decode($client->active_channels, true);



        $channels = DB::table('canales')->where('isActive', '=', 1)->pluck('name')->toArray();


        $datas = DB::table('estrategias')
            ->select('id', 'onlyWhere', 'table_name', 'channels', 'isActive', 'isDelete', 'type', 'repeatUsers', 'activation_date', 'activation_time')
            ->where('prefix_client', '=', $client->prefix)
            ->orderBy('isActive', 'DESC')
            ->orderBy('type', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->get();

        // return $datas;



        $total_cartera = 0;
        $suma_total = 0;
        $porcentaje_total = 0;

        $queries = [];
        $merge = [];

        $data_counter = count($datas);


        if ($data_counter > 0) {
            $total_cartera = DB::select("SELECT COUNT(*) AS total_cartera FROM " . $datas[0]->table_name)[0]->total_cartera;

            foreach ($datas as $key => $val) {
                if ($val->isDelete !== 1) {
                    $queries[$key][] = $val->table_name;
                    $queries[$key][] = $val->onlyWhere;
                }
            }

            $queryResults = (new EstrategiaController)->queryResults($queries);

            
            
            $merge = [];
            $unicos_unicos = [];

            for ($i = 0; $i < count($queryResults); $i++) {
                $tempArray = [];
                
                for ($j = 0; $j < count($queryResults); $j++) {
                    if ($i !== $j) {
                        $tempArray = array_merge($tempArray, $queryResults[$j]);
                    }
                }
                $merge[$i] = array_unique(array_merge($tempArray));

                
                
                $unicos[] = array_diff($queryResults[$i], $merge[$i]);
                $repetidos[] = array_intersect($queryResults[$i], $merge[$i]);

                $total_unicos[] = count(array_diff($queryResults[$i], $merge[$i]));
                $total_repetidos[] = count(array_intersect($queryResults[$i], $merge[$i]));

                $percent_cober[] = (count(array_diff($queryResults[$i], $merge[$i]))/$total_cartera)*100;
                $total_r[] = count($queryResults[$i]);

                $result = [
                    // 'unicos' =>$unicos,
                    // 'repetidos' => $repetidos,
                    'total_unicos' =>$total_unicos,
                    'total_repetidos' =>$total_repetidos,
                    'percent_cober' =>$percent_cober,
                    'total_r' => $total_r
                ];
                // $repetidos[] = count(array_merge($merge));
            }


                // return $result;




            $c = 0;

            foreach ($datas as $key => $data) {
                if (isset($channels[$data->channels])) {
                    $data->canal = $channels[$data->channels];
                }


                if ($data->isDelete === 0) {
                    $data->registros_unicos = $unicos[$c];
                    
                    $data->total_registros_unicos = $total_unicos[$c];
                    $data->porcentaje_registros_unicos = $percent_cober[$c];
                    $data->repetidos = $total_repetidos[$c];
                    // if ($c < count($queryResults) - 1) {
                    //     $data->repetidos = count($queryResults[$c]['total_r']) - count($queryResults[$c + 1]['total_r']);
                    // }
                }
                if ($c < count($queryResults) - 1) {
                    $c++;
                }


                if ($data->isActive === 1 && $data->type === 2) {
                    $suma_total += $data->total_registros_unicos;
                    $porcentaje_total += $data->porcentaje_registros_unicos;
                }
            }


            if ($client->active_channels !== null) {
                foreach ($client->active_channels as $k => $v) {
                    $ch_approve[] = $v['seleccionado'];
                }
                $client->active_channels = $ch_approve;
            } else {
                $client->active_channels = [];
            }
        }



        // return $datas;


        $config_layout = [
            'title-section' => 'Cliente: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name,
            'btn-back' => 'clients.index'
        ];

        return view('clients/show', compact('config_layout', 'client', 'datas', 'channels', 'suma_total', 'porcentaje_total'));
    }
}
