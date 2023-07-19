<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Config\UserController;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;

class ClientController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:root-list|clients-list', ['only' => ['index', 'show', 'getClientData']]);
        $this->middleware('permission:root-create|clients-create', ['only' => ['disenoEstrategia']]);
        $this->middleware('permission:root-edit|clients-edit', ['only' => ['edit', 'update']]);
    }


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
        $channels =  Cache::get('canales');
        $channels_config = $result[1];

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
        
        Cache::forget('canales');
        Cache::forever('canales', $responses['canales']->collect()[0]);


        

        // Obtengo los canales activos
        

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
        $channels = Cache::get('canales');
        $channels_config = $result[1];

        Cache::forget('estrategias');
        Cache::forget('estructura');
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->as('estrategias')->get(env('API_URL') .  env('API_ESTRATEGIAS') . '/diseno/' . strtoupper($client->prefix)),
            $pool->as('estructura')->get(env('API_URL') .  env('API_ESTRUCTURA') . '/' . $id),

        ]);
        Cache::forever('estrategias', $responses['estrategias']->collect()[0]);
        Cache::forever('estructura', $responses['estructura']->collect()[0]);


        $estrategias_cache = Cache::get('estrategias');
        $estructura_cache = Cache::get('estructura');



        $datas =  $estrategias_cache;
        $estructura = $estructura_cache;

        for ($i = 0; $i < count($datas); $i++) {
            $datas[$i]['registros'] = count(json_decode($datas[$i]['registros'], true));
        }

        //Configuramos la vista
        $config_layout = [
            'title-section' => 'Diseño de estrategia para: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name . ' > Diseño de estrategia',
            'btn-back' => 'clients.show'
        ];

        $ch_approve = [];

        if (count($datas) > 0) {
            foreach ($datas as $key => $data) {
                if ($data['type'] === 0) {
                    $canales[] = $data['channels'];
                }

                if (in_array($data['channels'], array_keys($channels))) {
                    $datas[$key]['canal'] = strtoupper($channels[$data['channels']]['name']);
                }

                if ($data['repeatUsers'] === 1) {
                    $datas[$key]['registros_t'] = $data['registros'];
                }

                if ($channels_config != null) {
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

        return view('clients/diseno', compact('client', 'datas', 'config_layout', 'channels', 'estructura', 'ch_approve', 'channels_config'));
    }

    public function show($id)
    {


        // Obtenemos datos y configuraciones del cliente. 
        $result = $this->getClientData($id);
        $client = $result[0];
        $channels = Cache::get('canales');
        $channels_config = $result[1];


        Cache::forget('estrategias');
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->as('estrategias')->get(env('API_URL') .  '/estrategias/' . strtoupper($client->prefix)),

        ]);
        Cache::forever('estrategias', $responses['estrategias']->collect()[0]);


        $estrategias_cache = Cache::get('estrategias');

        $datas =  $estrategias_cache;

        for ($i = 0; $i < count($datas); $i++) {
            $datas[$i]['registros'] = count(json_decode($datas[$i]['registros'], true));
        }

        $total_cartera = 0;
        $suma_total = 0;
        $porcentaje_total = 0;
        $ch_approve = [];
        $data_counter = count($datas);

        if ($data_counter > 0) {
            foreach ($datas as $key => $data) {
                if (in_array($data['channels'], array_keys($channels))) {
                    $datas[$key]['canal'] = strtoupper($channels[$data['channels']]['name']);
                }

                if ($data['type'] === 2) {
                    if ($data['repeatUsers'] === 0) {
                        $suma_total += $data['registros_unicos'];
                    } else {
                        $data->registros_t = $data['registros'];
                        $suma_total += $data['registros_t'];
                    }
                    $porcentaje_total += $data['cobertura'];
                }
            }

            if ($channels_config != null) {
                if ($data['type'] === 0) {
                    $key_active_channels = array_keys($channels_config);
                    $ch_approve = array_diff($key_active_channels, $channels);
                } else {
                    $ch_approve = array_keys($channels_config);
                }
            }
        }

        $config_layout = [
            'title-section' => 'Cliente: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name,
            'btn-back' => 'clients.index'
        ];



        return view('clients/show', compact('config_layout', 'client', 'datas', 'channels', 'ch_approve', 'porcentaje_total', 'suma_total'));
    }
}
