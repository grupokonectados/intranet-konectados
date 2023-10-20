<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Config\UserController;
use Illuminate\Http\Request;
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
        $this->getClientData($id);

        $client = Cache::get('cliente');
        $channels =  Cache::get('canales');
        $channels_config = Cache::get('config_channels');
        $estructura = Cache::get('estructura');

        $config_layout = [
            'title-section' => 'Editar: ' . $client->name,
            'breads' => 'Clientes > Editar: ' . $client->name,
            'btn-back' => 'clients.show'
        ];

        return view('clients/edit', compact('client', 'config_layout', 'channels', 'channels_config', 'estructura'));
    }

    public function update(Request $request, $id)
    {

        $update = [];
        $update['idClient'] = intval($id);
        $update['channels'] = json_encode($request['configuracion'], JSON_FORCE_OBJECT);


        // return $update;
        $updated = Http::put(env('API_URL') . env('API_CLIENT') . "/canales", $update);
        if ($updated != 'false') {
            return redirect(route('clients.show', $id));
        } else {
            return $updated;
        }
    }

    function getClientData($id)
    {

        $responses = Http::pool(fn (Pool $pool) => [
            $pool->as('clientes')->get(env('API_URL') . env('API_CLIENTS')),
            $pool->as('canales')->get(env('API_URL') . env('API_CHANNELS')),
            $pool->as('estructura')->get(env('API_URL') .  env('API_ESTRUCTURA') . '/' . $id),
        ]);

        foreach ($responses['clientes']->json()[0] as $value) {
            if (in_array($id, $value)) {
                $arr = $value;
            }
        }

        $getConfigMails = Http::get(env('API_URL') . env('API_EMAILS') . $arr['prefix']);
        $config_mail = $getConfigMails->json()[0];

        $getList = Http::get(env('API_URL') . '/listasdiscador/' . $arr['prefix']);
        $list_disc = $getList->json()[0];

        Cache::forget('canales');
        Cache::forget('cliente');
        Cache::forget('estructura');
        Cache::forget('config_channels');
        Cache::forget('config_mail');
        Cache::forget('list_discador');

        Cache::forever('canales', $responses['canales']->collect()[0]);
        Cache::forever('cliente', json_decode(json_encode($arr, JSON_FORCE_OBJECT)));
        Cache::forever('estructura', $responses['estructura']->collect()[0]);

        Cache::forever('config_channels', json_decode($arr['channels'], true));
        Cache::forever('config_mail', $config_mail);
        Cache::forever('list_discador', $list_disc);
    }

    public function disenoEstrategia($id)
    {

        $ch_approve = [];
        $suma_total = 0;
        $porcentaje_total = 0;



        // return $this->getClientData($id);

        $client = Cache::get('cliente');
        $channels = Cache::get('canales');
        $channels_config = Cache::get('config_channels');
        $estructura = Cache::get('estructura');
        $config_mail_cache = Cache::get('config_mail');
        $lista_discadores = Cache::get('list_discador');




        $config_mail = [];
        for ($count = 0; $count < count($config_mail_cache); $count++) {
            $config_mail[$count]['id'] = $config_mail_cache[$count]['id'];
            $config_mail[$count]['nombreTemplate'] = $config_mail_cache[$count]['nombreTemplate'];
        }


        // return $lista_discadores;

        $estrc = [];

        foreach ($estructura as $ki => $vi) {
            if (in_array($vi['COLUMN_NAME'], array_keys($channels_config['estructura']))) {
                if (isset($channels_config['estructura'][$vi['COLUMN_NAME']]['utilizar'])) {
                    $vi['NAME'] = $channels_config['estructura'][$vi['COLUMN_NAME']]['nombre'];
                    $estrc[] = $vi;
                }
            }
        }

        $param = [
            'prefix' => $client->prefix,
            'type' => 1
        ];

        // Configuramos la vista
        $config_layout = [
            'title-section' => 'Diseño de estrategia para: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name . ' > Diseño de estrategia',
            'btn-back' => 'clients.show'
        ];

        Cache::forget('estrategias');

        $responses = Http::pool(function (Pool $pool) use ($client) {
            $pool->as('estrategias')->get(env('API_URL') . env('API_ESTRATEGIAS') . '/diseno/' . strtoupper($client->prefix));
        });


        Cache::forever('estrategias', $responses['estrategias']->json()[0]);
        $datas = Cache::get('estrategias');

        foreach ($datas as &$d3) {
            unset($d3['registros']);
        }

        // return $datas;

        if (count($datas) > 0) {
            $canales = [];
            foreach ($datas as $key => $data) {
                if ($data['type'] === 0) {
                    $canales[] = $data['channels'];
                }

                if (isset($channels[$data['channels']])) {
                    $datas[$key]['canal'] = strtoupper($channels[$data['channels']]['name']);
                }



                if ($channels_config != null) {
                    if ($data['type'] === 0) {
                        $key_active_channels = array_keys($channels_config['channels']);
                        $ch_approve = array_diff($key_active_channels, $canales);
                    } else {
                        $ch_approve = array_keys($channels_config['channels']);
                    }
                }
            }
        } else {
            if ($channels_config['channels'] != null) {
                $ch_approve = array_keys($channels_config['channels']);
            } else {
                $channels_config = [];
            }
        }

        // return $datas;

        return view('clients/diseno', compact('lista_discadores', 'config_mail', 'client', 'datas', 'porcentaje_total',  'suma_total', 'config_layout', 'channels', 'estrc', 'ch_approve', 'channels_config'));
    }

    public function show($id)
    {
        Cache::forget('estrategias');

        // Obtenemos datos y configuraciones del cliente.
        // $this->getClientData($id);
        $client = Cache::get('cliente');
        $channels = Cache::get('canales');
        $channels_config = Cache::get('config_channels');

        $responses = Http::timeout(120)->get(env('API_URL') . env('API_ESTRATEGIAS') . '/' . strtoupper($client->prefix));
        $datas = $responses->json()[0];

        Cache::forever('estrategias', $datas);



        $total_cartera = 0;
        $suma_total = 0;
        $porcentaje_total = 0;
        $ch_approve = [];
        $groupedArray = [];
        $data_counter = count($datas);

        if ($data_counter > 0) {
            foreach ($datas as $key => $data) {
                if (isset($channels[$data['channels']])) {
                    $datas[$key]['canal'] = strtoupper($channels[$data['channels']]['name']);
                }

                if ($data['type'] === 2) {
                    if ($data['repeatUsers'] === 0) {
                        $suma_total += $data['registros_unicos'];
                    } else {
                        $suma_total += $data['total_registros'];
                    }
                    $porcentaje_total += $data['cobertura'];
                }
            }

            if ($channels_config['channels'] != null) {
                if ($data['type'] === 0) {
                    $key_active_channels = array_keys($channels_config['channels']);
                    $ch_approve = array_diff($key_active_channels, $channels);
                } else {
                    $ch_approve = array_keys($channels_config['channels']);
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
