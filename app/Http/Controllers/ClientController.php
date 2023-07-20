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
            $pool->as('clientes')->get(env('API_URL') . env('API_CLIENTS')),
            $pool->as('canales')->get(env('API_URL') . env('API_CHANNELS')),
            $pool->as('estructura')->get(env('API_URL') .  env('API_ESTRUCTURA') . '/' . $id),
        ]);

        foreach ($responses['clientes']->collect()[0] as $value) {
            if (in_array($id, $value)) {
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

        $updated = Http::put(env('API_URL') . env('API_CLIENT') . "/canales", $update);

        // return $updated;

        if ($updated != 'false') {
            return redirect(route('clients.show', $id));
        } else {
            return $updated;
        }
    }

    public function disenoEstrategia($id)
    {

        $ch_approve = [];
        $suma_total = 0;
        $porcentaje_total = 0;

        $this->getClientData($id);

        $client = Cache::get('cliente');
        $channels = Cache::get('canales');
        $channels_config = Cache::get('config_channels');
        $estructura = Cache::get('estructura');

        $param = [
            'prefix' => $client->prefix,
            'type' => 1
        ];

        $estrategiasHistoricoCliente = Http::withBody(json_encode($param))->get(env('API_URL') . env('API_ESTRATEGIA') . '/tipo');
        $datas2 = $estrategiasHistoricoCliente->collect()[0];

        foreach ($datas2 as &$d2) {
            unset($d2['registros']);
            $suma_total += ($d2['repeatUsers'] === 0) ? $d2['registros_unicos'] : $d2['total_registros'];
            $porcentaje_total += $d2['cobertura'];
        }

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

        $estrategias = $responses['estrategias']->collect()[0];

        foreach ($estrategias as &$d3) {
            unset($d3['registros']);
        }

        Cache::forever('estrategias', $estrategias);
        $datas = Cache::get('estrategias');

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
            } else {
                $channels_config = [];
            }
        }



        return view('clients/diseno', compact('client', 'datas', 'porcentaje_total',  'suma_total', 'config_layout', 'channels', 'estructura', 'ch_approve', 'channels_config'));
    }

    public function show($id)
    {
        Cache::forget('estrategias');

        // Obtenemos datos y configuraciones del cliente.
        $this->getClientData($id);
        $client = Cache::get('cliente');
        $channels = Cache::get('canales');
        $channels_config = Cache::get('config_channels');

        $responses = Http::timeout(120)->get(env('API_URL') . env('API_ESTRATEGIAS') . '/' . strtoupper($client->prefix));
        $x = $responses->json()[0];

        foreach ($x as &$item) {
            unset($item['registros']);
        }

        Cache::forever('estrategias', $x);
        $estrategias_cache = Cache::get('estrategias');
        $datas = $estrategias_cache;

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
