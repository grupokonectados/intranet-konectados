<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use App\Models\Estructura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClientController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:root-list|clients-list', ['only' => ['index']]);
        $this->middleware('permission:root-create|clients-create', ['only' => ['create', 'store', 'disenoEstrategia']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:root-delete|clients-delete', ['only' => ['destroy']]);
    }


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

        $channels = \DB::table('canales')->pluck('name')->toArray();

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

        $client = Client::find($id); //Traigo los datos del cliente

        // convierto en un array los canales permitidos del cliente
        $client->active_channels = json_decode($client->active_channels, true);

        // Obtengo todos las estrategias que el cliente tiene que no estan activas
        $datas = Estrategia::where('prefix_client', '=', $client->prefix)
            ->where('isActive', '=', 0)
            ->where('type', '=', 0)
            ->get();


        


        $calculoEstrategias = $this->calculoEstrategias($datas);


        return $calculoEstrategias;

        $estructura = Estructura::select('COLUMN_NAME', 'COLUMN_TYPE', 'DATA_TYPE', 'TABLE_NAME')->where("PREFIX", '=', $client->prefix)->get();


        // return $estructura;

        $contador = $calculoEstrategias['contador'];
        $dataChart = $calculoEstrategias['dataChart'];
        $cuenta_total = $calculoEstrategias['cuenta_total'];

        $x = [];

        foreach ($datas as $key => $value) {
            if ($value->repeatUsers === 1) {
                $x[] = count($contador['results_querys'][$key]);
            }
        }


        $total_resta = 0;

        if (count($datas) > 1 && count($x) > 0) {

            rsort($x);

            // return $x;
            $total_resta =  $x[0];

            for ($i = 1; $i < count($x); $i++) {
                $total_resta -= $x[$i];
            }
        } else {
            $total_resta = 0;
        }

        $config_layout = [
            'title-section' => 'Diseño de estrategia para: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name . ' > Diseño de estrategia',
            'btn-back' => 'clients.show'
        ];

        return view('clients/diseno', compact('total_resta', 'client', 'datas', 'config_layout', 'estructura', 'multiples', 'channels', 'arr_c', 'contador', 'dataChart', 'cuenta_total'));
    }

    function calculoEstrategias($data)
    {

        $contador = [];
        $dataChart = [];
        $suma_total_results = 0;
        $suma_total_diff = 0;
        if (count($data) > 0) {

            $contador = (new EstrategiaController)->queryResults($data);

            return ($contador);
            $total_cartera = \DB::select("select count(*) as total_cartera from " . $data[0]->table_name)[0]->total_cartera;

            $pos = 0;

            if (count($contador['results_querys']) > 0) {
                for ($o = 0; $o < count($contador['results_querys']); $o++) {
                    switch ($data[$o]->channels) {
                        case 1:
                            $title = 'AGENTE';
                            break;
                        case 2:
                            $title = 'EMAIL';
                            break;
                        case 3:
                            $title = 'IVR';
                            break;
                        case 4:
                            $title = 'SMS';
                            break;
                        case 5:
                            $title = 'VOICE BOT';
                            break;
                        case 6:
                            $title = 'WHATSAPP';
                            break;
                    }

                    //    return $contador['diff_query'];

                    if ($data[$o]->repeatUsers == 0 && count($contador['diff_query']) > 0) {
                        if (count($contador['diff_query'][$o + 1]) > 0) {
                            $dataChart[$pos] = [
                                'title' => $title,
                                'datos' => number_format(count($contador['diff_query'][$o + 1]), 0, ',', '.'),
                                'porcentaje' => number_format((count($contador['diff_query'][$o + 1]) / $total_cartera) * 100, 2)
                            ];
                            $suma_total_diff += count($contador['diff_query'][$o + 1]);
                        } else {
                            $dataChart[$pos] = [
                                'title' => $title,
                                'datos' => number_format(count($contador['results_querys'][$o]), 0, ',', '.'),
                                'porcentaje' => number_format((count($contador['results_querys'][$o]) / $total_cartera) * 100, 2)
                            ];
                            $suma_total_results += count($contador['results_querys'][$o]);
                        }
                    } else {
                        $dataChart[$pos] = [
                            'title' => $title,
                            'datos' => number_format(count($contador['results_querys'][$o]), 0, ',', '.'),
                            'porcentaje' => number_format((count($contador['results_querys'][$o]) / $total_cartera) * 100, 2)
                        ];
                        $suma_total_results += count($contador['results_querys'][$o]);
                    }
                    $pos++;
                }
            } else {
                $contador = ['total' => 0,];
                $dataChart = [];
            }
            $cuenta_total = [
                'total_results' => $suma_total_results,
                'total_diff' => $suma_total_diff,
                'total' => number_format(($suma_total_results + $suma_total_diff), 0, ',', '.'),
                'porcentual' => number_format((($suma_total_results + $suma_total_diff) / $total_cartera) * 100, 2, ',', '.')
            ];
        } else {

            $cuenta_total = [
                'total_results' => 0,
                'total_diff' => 0,
                'total' => 0,
                'porcentual' => 0
            ];
        }

        return [
            'contador' => $contador,
            'dataChart' => $dataChart,
            'cuenta_total' => $cuenta_total,
        ];
    }

    public function probarConsulta(Request $request)
    {

        $data = Estrategia::where('prefix_client', '=', $request->prefix)
            ->where('type', '=', 0)
            ->get();

        $calculoEstrategias = $this->calculoEstrategias($data);


        return [
            'data1' => $calculoEstrategias['dataChart'],
            'data2' => $calculoEstrategias['cuenta_total'],
            'data3' => $data,
            'data4' => $calculoEstrategias['contador']
        ];
    }

    public function show($id, Request $request)
    {

        // return $request;

        /**
         * Metodo laravel
         */

        $client = Client::find($id);

        $channels = \DB::table('canales')->pluck('name')->toArray();


        if ($request) {
            if ($request->channelorder) {
                $dataEstrategias = Estrategia::where('prefix_client', '=', $client->prefix)
                    ->where('type', '=', 2)
                    ->where('isActive', '=', 1)
                    ->orderBy('channels', $request->channelorder)
                    ->get();
            } elseif ($request->dateorder) {
                $dataEstrategias = Estrategia::where('prefix_client', '=', $client->prefix)
                    ->where('type', '=', 2)
                    ->where('isActive', '=', 1)
                    ->orderBy('activation_time', $request->dateorder)
                    ->get();
            } else {
                $dataEstrategias = Estrategia::where('prefix_client', '=', $client->prefix)
                    ->where('type', '=', 2)
                    ->where('isActive', '=', 1)
                    ->get();
            }
        } else {
        }




        if ($request) {
            if ($request->channelnotorder) {
                $dataEstrategiasNot = Estrategia::where('prefix_client', '=', $client->prefix)
                    ->where('type', '=', 2)
                    ->where('isDelete', '=', 1)
                    ->orderBy('channels', $request->channelnotorder)
                    ->get();
            } elseif ($request->datenotorder) {
                $dataEstrategiasNot = Estrategia::where('prefix_client', '=', $client->prefix)
                    ->where('type', '=', 2)
                    ->where('isDelete', '=', 1)
                    ->orderBy('activation_time', $request->datenotorder)
                    ->get();
            } else {
                $dataEstrategiasNot = Estrategia::where('prefix_client', '=', $client->prefix)
                    ->where('type', '=', 2)
                    ->where('isDelete', '=', 1)
                    ->get();
            }
        } else {
            $dataEstrategiasNot = Estrategia::where('prefix_client', '=', $client->prefix)
                ->where('type', '=', 2)
                ->where('isDelete', '=', 1)
                ->get();
        }





        $calculoEstrategias = $this->calculoEstrategias($dataEstrategias);

        // return $calculoEstrategias;


        $contador = $calculoEstrategias['contador'];
        $dataChart = $calculoEstrategias['dataChart'];
        $cuenta_total = $calculoEstrategias['cuenta_total'];

        $x = [];

        foreach ($dataEstrategias as $key => $value) {
            if ($value->repeatUsers === 1) {
                $x[] = count($contador['results_querys'][$key]);
            }
        }

        if (count($dataEstrategias) > 1 && count($x) > 0) {

            rsort($x);
            $total_resta =  $x[0];

            for ($i = 1; $i < count($x); $i++) {
                $total_resta -= $x[$i];
            }
        } else {
            $total_resta = 0;
        }
        // return $total_resta;





        $config_layout = [
            'title-section' => 'Cliente: ' . $client->name,
            'breads' => 'Clientes > ' . $client->name,
            'btn-back' => 'clients.index'
        ];

        return view('clients/show', compact('config_layout', 'total_resta', 'client', 'dataEstrategias', 'contador', 'dataEstrategiasNot', 'dataChart', 'cuenta_total', 'channels'));
    }
}
