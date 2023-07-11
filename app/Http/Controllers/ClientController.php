<?php

namespace App\Http\Controllers;

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
        /**
         * Metodo laravel
         */
        

         

        //  if(Gate::check('root-list')){
        //     $data = Client::all();
        //  }else{
        //     if(auth()->user()->ve_clientes !== null){
        //         $clientes = json_decode(auth()->user()->ve_clientes, true);
        //         $data = Client::whereIn('id', $clientes)->get();
        //     }else{
        //         $data = [];
        //     }
            
            
        //  }

        /**
         * Metodo API
         */

        $response = Http::get(env('API_URL').env('API_CLIENTS'));
        $data = $response->json()[0];


        $config_layout = [
            'title-section' => 'Clientes',
            'breads' => 'Clientes',
        ];

        return view('clients/index', compact('data', 'config_layout'));
    }


    public function edit($id)
    {

        // return $result = $this->getClientData($id);


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



    function getClientData($id){

        /**
         * Cuando el enpoint que entrega los datos de un cliente unico, cambiar el metodo
         * de consulta del cliente por el endpoint de la API 
         */


        $client = Client::find($id);


        $responses = Http::pool(fn (Pool $pool) => [
            //  $pool->as('clientes')->get(env('API_URL').env('API_CLIENTS')),
             $pool->as('canales')->get(env('API_URL').env('API_CHANNELS')),
         ]);
       
        $channels = $responses['canales']->json()[0];



        if(count(DB::table('config_clients')->select('channels_config')->where('client_id', '=', $id)->get())>0){
            $config_clients = DB::table('config_clients')->select('channels_config')->where('client_id', '=', $id)->get()[0];
            $channels_config = json_decode($config_clients->channels_config, true);
        }else{ 
            $config_clients = [];
            $channels_config = [];
        }

        
        


        return [
            $client,
            $channels,
            $channels_config,
        ];
    }


    public function update(Request $request, $id)
    {

        

        $conf_client = DB::table('config_clients')->where('client_id', '=', $id)->get();

        if(count($conf_client) === 0){
            DB::insert('insert into config_clients (client_id, channels_config) values (?, ?)', [$id, json_encode($request['channels'])]);
        }else{
            DB::update('update config_clients set channels_config = ? where client_id = ?', [json_encode($request['channels']), $id]);
        }

        // ds($conf_client);

        // $conf_client->save();

        // return $conf_client;


        // $client = Client::find($id);
        // $client->active_channels = json_encode($request['channels']);
        // $client->save();

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
        ->select('id', 'channels', 'onlyWhere', 'isActive', 'isDelete', 'type', 'repeatUsers', 'cobertura', 'registros_unicos', 'registros_repetidos', 'registros')
        ->where('prefix_client', '=', $client->prefix)
        ->whereIn('isActive', [0, 1])
        ->whereIn('type', [0, 1, 2])
        ->where('isDelete', '=', 0)
        ->orderBy('isActive', 'DESC')
        ->orderBy('type', 'ASC')
        ->orderBy('created_at', 'ASC')
        ->get();

        $queries = [];
        $ch_approve = [];

        if (count($datas) > 0) { 

            foreach ($datas as $key => $data) {

                if ($data->type === 0) {
                    $canales[] = $data->channels;
                }

                if (isset($channels[$data->channels])) {
                    $data->canal = $channels[$data->channels];
                }

                if($data->repeatUsers === 1){
                    $data->registros_t = count(json_decode($data->registros, true));
                }
                
                unset($data->registros);

                if ($client->active_channels != null) {
                    if ($data->type === 0) {
                        $key_active_channels = array_keys($client->active_channels);

                        $ch_approve = array_diff($key_active_channels, $canales);
                    } else {
                        $key_active_channels = array_keys($client->active_channels);
                        $ch_approve = $key_active_channels;
                    }
                } else {
                    $ch_approve = [];
                    $client->active_channels = [];
                }
            }

        } else {

            if ($client->active_channels != null) {
                $key_active_channels = array_keys($client->active_channels);

                $ch_approve = $key_active_channels;
            } else {
                $ch_approve = [];
                $client->active_channels = [];
            }
        }

        // return $datas;


        return view('clients/diseno', compact('client', 'datas', 'config_layout', 'channels', 'estructura', 'ch_approve'));
    }




    public function show($id)
    {

        

        

      
        $client = Client::select('id', 'name', 'prefix')->find($id); //Traigo los datos del cliente

        // convierto en un array los canales permitidos del cliente
        // $client->active_channels = json_decode($client->active_channels, true);





        $channels = DB::table('canales')->where('isActive', '=', 1)->pluck('name')->toArray();


        $datas = DB::table('estrategias')
            ->select('id', 'channels', 'onlyWhere', 'isActive', 'isDelete', 'type', 'repeatUsers', 'cobertura', 'registros_unicos', 'registros_repetidos', 'activation_date', 'activation_time', 'registros')
            ->where('prefix_client', '=', $client->prefix)
            ->orderBy('isActive', 'DESC')
            ->orderBy('type', 'ASC')
            ->orderBy('activation_date', 'DESC')
            ->orderBy('activation_time', 'DESC')
            ->get();

        $total_cartera = 0;
        $suma_total = 0;
        $porcentaje_total = 0;
        $data_counter = count($datas);

        $queries = [];
        $ch_approve = [];

        // return $datas;

        if ($data_counter > 0) {
            
            
            foreach ($datas as $key => $data) {
                if (isset($channels[$data->channels])) {
                    $data->canal = $channels[$data->channels];
                }


                if($data->type === 2){
                    if($data->repeatUsers === 0){
                    $suma_total += $data->registros_unicos;
                    }else{
                        $data->registros_t = count(json_decode($data->registros, true));
                        $suma_total +=$data->registros_t;
                    }
                    $porcentaje_total += $data->cobertura;
                }

                unset($data->registros);
            }


            if ($client->active_channels !== null) {
                foreach ($client->active_channels as $k => $v) {
                    $ch_approve[] = $v['seleccionado'];
                }
                $client->active_channels = $ch_approve;
            } else {
                $client->active_channels = [];
                $ch_approve = [];
            }
        }


        // return $ch_approve;

        $config_layout = [
            'title-section' => 'Cliente: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name,
            'btn-back' => 'clients.index'
        ];



        return view('clients/show', compact('config_layout', 'client', 'datas', 'channels', 'ch_approve', 'porcentaje_total', 'suma_total'));
    }
}
