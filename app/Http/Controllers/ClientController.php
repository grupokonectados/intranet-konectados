<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Config\UserController;
use App\Models\Client;
use App\Models\Estructura;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


use Illuminate\Http\Client\Pool;

class ClientController extends Controller
{

    const PATH_API = '/clientes';

    public function index()
    {
        $data = (new UserController)->getClienteUser();

        $config_layout = [
            'title-section' => 'Clientes',
            'breads' => 'Clientes',
        ];

        return view('clients/index', compact('data', 'config_layout'));
    }


    public function edit($id)
    {
        $result = $this->getClientData($id);

        $client = $result[0];
        $channels = $result[1];
        $channels_config = $result[2];

        $config_layout = [
            'title-section' => 'Editar: ' . $client->name,
            'breads' => 'Clientes > Editar: ' . $client->name,
            'btn-back' => 'clients.show'
        ];

        return view('clients/edit', compact('client', 'config_layout', 'channels', 'channels_config'));
    }



    function getClientData($id)
    {

        /**
         * Funcion para obtener los recursos necesarios para procesar al cliente una uni vez en distintos tiempos de ejecucion. 
         * 
         * Vista Editar
         * Vista Show
         * vista Disenio
         * 
         * Cuando el enpoint que entrega los datos de un cliente unico, cambiar el metodo
         * de consulta del cliente por el endpoint de la API 
         */


        $client = Client::find($id); // datos del cliente, eliminar cuando exista el endpoint


        $config_clients = [];
        $channels_config = [];

        // Consultamos mediante un pool de peticiones, lo hacemos asi para obtener todos los datos en un solo arreglo de datos y 
        // no tener que consultar uno a uno los datos del cliente y los canales activos.
        $responses = Http::pool(fn (Pool $pool) => [
            //  $pool->as('clientes')->get(env('API_URL').env('API_CLIENTS')), 
            $pool->as('canales')->get(env('API_URL') . env('API_CHANNELS')),
        ]);

        // Obtengo los canales activos
        $channels = $responses['canales']->json()[0];

        // Verifico la configuracion del cliente para conocer los canales habilitados para su uso.
        $config_clients = DB::table('config_clients')->select('channels_config')->where('client_id', '=', $id)->count();

        //Verificamos que el cliente tenga canales habilitados
        if ($config_clients > 0) { //Caso positivo

            // la configuracion retornada la almacenamos en un arreglo
            $channels_config = json_decode(DB::table('config_clients')->select('channels_config')->where('client_id', '=', $id)->get()[0]->channels_config, true);
        }

        // Retornamos los datos del cliente y los canales de manera general y la configuracion de los canales habilitados para el cliente.
        return [
            $client,
            $channels,
            $channels_config,
        ];
    }


    public function update(Request $request, $id)
    {

        /**
         * Tengo que esperar el Endpoint de update
         */

        $conf_client = DB::table('config_clients')->where('client_id', '=', $id)->get();

        if (count($conf_client) === 0) {
            DB::insert('insert into config_clients (client_id, channels_config) values (?, ?)', [$id, json_encode($request['channels'])]);
        } else {
            DB::update('update config_clients set channels_config = ? where client_id = ?', [json_encode($request['channels']), $id]);
        }


        return redirect(route('clients.show', $id));
    }

    public function disenoEstrategia($id)
    {


        $result = $this->getClientData($id);

        $client = $result[0];
        $channels = $result[1];
        $channels_config = $result[2];


        $response = Http::get(env('API_URL') . env('API_ESTRATEGIA') . '/' . strtoupper($client->prefix));
        $datas =  $response->collect()[0];

        for ($i = 0; $i < count($datas); $i++) {
            $datas[$i]['registros'] = count(json_decode($datas[$i]['registros'], true));
        }



        // Traemos la estructura de la tabla





        $estructura = Http::get(env('API_URL') . env('API_ESTRUCTURA') . '/' . $id)->json()[0];

        //Configuramos la vista
        $config_layout = [
            'title-section' => 'Diseño de estrategia para: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name . ' > Diseño de estrategia',
            'btn-back' => 'clients.show'
        ];

        // Obtengo todos las estrategias que el cliente tiene que no estan activas
        // $datas = DB::table('estrategias')
        //     ->select(
        //         DB::raw('id, channels, onlyWhere, isActive, isDelete, type, repeatUsers, cobertura, registros_unicos, registros_repetidos, JSON_LENGTH(registros) as registros')
        //     )
        //     // id, channels, onlyWhere, isActive, isDelete, type, repeatUsers, cobertura, registros_unicos, registros_repetidos, 'count(registros)')
        //     ->where('prefix_client', '=', $client->prefix)
        //     ->where('type', '!=', 3)
        //     ->where('isDelete', '!=', 1)
        //     ->orderBy('isActive', 'DESC')
        //     ->orderBy('type', 'ASC')
        //     ->orderBy('created_at', 'ASC')
        //     ->get();


        // return $datas[0]['channels'];
        // return $channels[0]['id'];

        // return in_array($datas[0]['channels'], array_keys($channels))? 'si' : 'no';

        $queries = [];
        $ch_approve = [];

        if (count($datas) > 0) {

            foreach ($datas as $key => $data) {

                // if ($data->type === 0) {
                if ($data['type'] === 0) {

                    // $canales[] = $data->channels;
                    $canales[] = $data['channels'];
                }

                if (in_array($data['channels'], array_keys($channels))) {
                    // $data->canal = $channels[$key]['name'];
                    $datas[$key]['canal'] = $channels[$data['channels']]['name'];

                    // array_push($data, ['canal' => 'agente']);
                }

                // if ($data->repeatUsers === 1) {
                // if ($data->repeatUsers === 1) {
                if ($data['repeatUsers'] === 1) {
                    // $data->registros_t = $data->registros;
                    $datas[$key]['registros_t'] = $data['registros'];
                }


                if ($channels_config != null) {
                    // if ($data->type === 0) {
                    if ($data['type'] === 0) {
                        $key_active_channels = array_keys($channels_config);
                        $ch_approve = array_diff($key_active_channels, $canales);
                    } else {
                        $ch_approve = array_keys($channels_config);
                    }
                }
            }
        } else {

            if ($channels_config != null) {
                $ch_approve = array_keys($channels_config);
            }
        }

        // return $datas;


        return view('clients/diseno', compact('client', 'datas', 'config_layout', 'channels', 'estructura', 'ch_approve', 'channels_config'));
    }




    public function show($id)
    {


        // Obtenemos datos y configuraciones del cliente. 
        $result = $this->getClientData($id);



        $client = $result[0];
        $channels = $result[1];
        $channels_config = $result[2];



        $response = Http::get(env('API_URL') . env('API_ESTRATEGIA') . '/' . strtoupper($client->prefix));
        $datas =  $response->collect()[0];

        for ($i = 0; $i < count($datas); $i++) {
            $datas[$i]['registros'] = count(json_decode($datas[$i]['registros'], true));
        }


        // return $datas;


        // convierto en un array los canales permitidos del cliente
        // $client->active_channels = json_decode($client->active_channels, true);


        // $datas = DB::table('estrategias')
        //     ->select(DB::raw('id, channels, onlyWhere, isActive, isDelete, type, repeatUsers, cobertura, registros_unicos, registros_repetidos, activation_date, activation_time, JSON_LENGTH(registros) as registros'))
        //     //'id', 'channels', 'onlyWhere', 'isActive', 'isDelete', 'type', 'repeatUsers', 'cobertura', 'registros_unicos', 'registros_repetidos', 'activation_date', 'activation_time', 'registros')
        //     ->where('prefix_client', '=', $client->prefix)
        //     ->where('type', '!=', 1)
        //     ->orderBy('isActive', 'DESC')
        //     ->orderBy('type', 'ASC')
        //     ->orderBy('activation_date', 'DESC')
        //     ->orderBy('activation_time', 'DESC')
        //     ->get();


        // return $datas;
        $total_cartera = 0;
        $suma_total = 0;
        $porcentaje_total = 0;
        $data_counter = count($datas);

        $queries = [];
        $ch_approve = [];


        if ($data_counter > 0) {


            foreach ($datas as $key => $data) {
                if (isset($channels[$key])) {
                    $data['canal'] = $channels[$key]['name'];
                    // $data->canal = $channels[$key]['name'];
                }

                if ($data['type'] === 2) {
                    // if ($data->type === 2) {
                    // if ($data->repeatUsers === 0) {
                    if ($data['repeatUsers'] === 0) {
                        $suma_total += $data->registros_unicos;
                    } else {
                        $data->registros_t = $data->registros;
                        $suma_total += $data->registros_t;
                    }
                    $porcentaje_total += $data->cobertura;
                }
            }


            if ($client->active_channels !== null) {
                foreach ($channels_config as $k => $v) {
                    $ch_approve[] = $v['seleccionado'];
                }
                $channels_config = $ch_approve;
            }
        }


        // return $datas;

        $config_layout = [
            'title-section' => 'Cliente: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name,
            'btn-back' => 'clients.index'
        ];



        return view('clients/show', compact('config_layout', 'client', 'datas', 'channels', 'ch_approve', 'porcentaje_total', 'suma_total'));
    }
}
