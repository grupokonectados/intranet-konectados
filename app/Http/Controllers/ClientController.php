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
        return $response;
    }



    public function disenoEstrategia($id)
    {

        $client = Client::select('id', 'name', 'prefix', 'active_channels')->find($id); //Traigo los datos del cliente

        // convierto en un array los canales permitidos del cliente
        $client->active_channels = json_decode($client->active_channels, true);

        $config_layout = [
            'title-section' => 'Diseño de estrategia para: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name . ' > Diseño de estrategia',
            'btn-back' => 'clients.show'
        ];

        $channels = DB::table('canales')->where('isActive', '=', 1)->pluck('name')->toArray();
        $estructura = Estructura::select('COLUMN_NAME', 'COLUMN_TYPE', 'DATA_TYPE', 'TABLE_NAME')->where("PREFIX", '=', $client->prefix)->get();



        // Obtengo todos las estrategias que el cliente tiene que no estan activas
        $datas = DB::table('estrategias')
            ->select('id', 'onlyWhere', 'table_name', 'channels', 'isActive', 'isDelete', 'type', 'repeatUsers')
            ->where('prefix_client', '=', $client->prefix)
            ->where('isActive', '=', 0)
            ->where('type', '=', 0)
            ->orderBy('created_at', 'ASC')
            ->get();

        $total_cartera = 0;

        $queries = [];
        $ch_approve = [];



        if (count($datas) > 0) {
            $total_cartera = DB::select("SELECT COUNT(*) AS total_cartera FROM " . $datas[0]->table_name)[0]->total_cartera;
            foreach ($datas as $key => $val) {
                $queries[$key][] = $val->table_name;
                $queries[$key][] = $val->onlyWhere;
            }
            $queryResults = (new EstrategiaController)->queryResults($queries);
            foreach ($datas as $key => $data) {
                if (isset($channels[$data->channels])) {
                    $data->canal = $channels[$data->channels];
                }
                $data->registros_unicos = $queryResults[$key][0];
                $data->total_registros_unicos = count($queryResults[$key][0]);
                $data->porcentaje_registros_unicos = (count($queryResults[$key][0]) / $total_cartera) * 100;
            }


            foreach ($client->active_channels as $k => $v) {
                $ch_approve[] = $v['seleccionado'];
            }

            $client->active_channels = $ch_approve;
        }



        // return $client;





        return view('clients/diseno', compact('client', 'datas', 'config_layout', 'channels', 'estructura'));
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
            ->orderBy('created_at', 'ASC')
            ->get();

        
            $total_cartera = 0;
        $totales = [];
        $suma_total = 0;
        $porcentaje_total = 0;


        if (count($datas) > 0) {
            $total_cartera = DB::select("SELECT COUNT(*) AS total_cartera FROM " . $datas[0]->table_name)[0]->total_cartera;
            foreach ($datas as $key => $val) {
                $queries[$key][] = $val->table_name;
                $queries[$key][] = $val->onlyWhere;
            }
            $queryResults = (new EstrategiaController)->queryResults($queries);
            foreach ($datas as $key => $data) {
                if (isset($channels[$data->channels])) {
                    $data->canal = $channels[$data->channels];
                }
                $data->registros_unicos = $queryResults[$key][0];
                $data->total_registros_unicos = count($queryResults[$key][0]);
                $data->porcentaje_registros_unicos = (count($queryResults[$key][0]) / $total_cartera) * 100;

                if($data->isActive === 1 && $data->type === 2){
                    $suma_total += $data->total_registros_unicos;
                    $porcentaje_total += $data->porcentaje_registros_unicos;
                }
                
            }


            foreach ($client->active_channels as $k => $v) {
                $ch_approve[] = $v['seleccionado'];
            }

            $client->active_channels = $ch_approve;
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
