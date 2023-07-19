<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Config\UserController;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use LDAP\Result;

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
        $this->getClientData($id);

        $client = Cache::get('cliente');
        $channels =  Cache::get('canales');
        $channels_config = Cache::get('config_channels');

        $config_layout = [
            'title-section' => 'Editar: ' . $client->name,
            'breads' => 'Clientes > Editar: ' . $client->name,
            'btn-back' => 'clients.show'
        ];

        return view('clients/edit', compact('client', 'config_layout', 'channels', 'channels_config'));
    }



    function getClientData($id)
    {
        
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->as('clientes')->get(env('API_URL').env('API_CLIENTS')), 
            $pool->as('canales')->get(env('API_URL') . env('API_CHANNELS')),
            $pool->as('estructura')->get(env('API_URL') .  env('API_ESTRUCTURA') . '/' . $id),
        ]);

        foreach($responses['clientes']->collect()[0] as $value){
            if(in_array($id, $value)){
                $arr = $value;
            }
            
        }
        
        
        Cache::forget('canales');
        Cache::forget('cliente');
        Cache::forget('estructura');
        Cache::forget('config_channels');

        Cache::forever('canales', $responses['canales']->collect()[0]);
        Cache::forever('cliente', json_decode(json_encode($arr, JSON_FORCE_OBJECT)));
        Cache::forever('estructura', $responses['estructura']->collect()[0]);

        Cache::forever('config_channels', json_decode($arr['channels'], true));

    }


    public function update(Request $request, $id)
    {

         $update = [];
         $update['idClient'] = intval($id);
         $update['channels'] = json_encode($request['channels'], JSON_FORCE_OBJECT);

         $updated = Http::put(env('API_URL').env('API_CLIENT')."/canales", $update);


         
        if($updated != 'false'){
            return redirect(route('clients.show', $id));
        }else{
            return $updated;
        }



        
    }

    public function disenoEstrategia($id)
    {

        $this->getClientData($id);


        

        $client = Cache::get('cliente');
        $channels = Cache::get('canales');
        $channels_config = Cache::get('config_channels');
        $estructura = Cache::get('estructura');


        
        
        Cache::forget('estrategias');
        

        $responses = Http::pool(fn (Pool $pool) => [
            $pool->as('estrategias')->get(env('API_URL') .  env('API_ESTRATEGIAS') . '/diseno/' . strtoupper($client->prefix)),
        ]);

        // return $responses['estrategias']->collect()[0];

        Cache::forever('estrategias', $responses['estrategias']->collect()[0]);



        
        $datas = Cache::get('estrategias');

        
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
            }else{
                $channels_config =[];
            }
        }



        return view('clients/diseno', compact('client', 'datas', 'config_layout', 'channels', 'estructura', 'ch_approve', 'channels_config'));
    }

    public function show($id)
    {
        Cache::forget('estrategias');

        // Obtenemos datos y configuraciones del cliente. 
       $this->getClientData($id);


        $client = Cache::get('cliente');
        $channels = Cache::get('canales');
        $channels_config = Cache::get('config_channels');



        
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->as('estrategias')->get(env('API_URL') . env('API_ESTRATEGIAS') . '/' . strtoupper($client->prefix)),
        ]);
        // return $responses['estrategias']->json();

        Cache::forever('estrategias', $responses['estrategias']->json()[0]);
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
