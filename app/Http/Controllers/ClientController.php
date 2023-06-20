<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estructura;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Http\Request;
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

        return view('clients/index', compact('data'));
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

        // $response = Http::post(env('API_URL') . '/estructura/search-estructura', $body_req);
        return $response;
    }

    public function show($id)
    {


        /**
         * Metodo laravel
         */

        $client = Client::find($id);
        $dataEstrategias = Client::select('e.*')
            ->join('estrategias as e', 'e.prefix_client', '=', 'clients.prefix')
            ->where('clients.id', '=', $id)
            ->where('e.isActive', '=', 1)
            ->get();


        $dataEstrategiasNot = Client::select('e.*')
            ->join('estrategias as e', 'e.prefix_client', '=', 'clients.prefix')
            ->where('clients.id', '=', $id)
            ->where('e.isActive', '=', 0)
            ->get();



        $arr = [];
        $i = 0;
        // return $dataEstrategias;
        foreach ($dataEstrategias as $key => $value) {
            if ($value['isActive'] == 1) {
                $arr[$i] = ['table_name' => $value['table_name'], 'where' => $value['onlyWhere']];
                $i++;
            }
        }

        if (count($dataEstrategias) > 0) {

            $contador = (new EstrategiaController)->queryResults($arr);

            if (count($contador) === 7) {
                for ($p = 0; $p < 3; $p++) {
                    switch ($dataEstrategias[$p]->channels) {
                        case 1:
                            $title = 'SMS';
                            break;
                        case 2:
                            $title = 'Llamada';
                            break;
                        case 3:
                            $title = 'Email';
                            break;
                    }
                    $dataChart[] = [
                        'title' => $title,
                        'datos' => $contador['sub' . $p + 1],
                    ];
                }
            } elseif (count($contador) === 5) {
                for ($p = 0; $p < 2; $p++) {

                    switch ($dataEstrategias[$p]->channels) {
                        case 1:
                            $title = 'SMS';
                            break;
                        case 2:
                            $title = 'Llamada';
                            break;
                        case 3:
                            $title = 'Email';
                            break;
                    }
                    $dataChart[] = [
                        'title' => $title,
                        'datos' => $contador['sub' . $p + 1],
                    ];
                }
            } else {
                for ($p = 0; $p < 1; $p++) {
                    switch ($dataEstrategias[$p]->channels) {
                        case 1:
                            $title = 'SMS';
                            break;
                        case 2:
                            $title = 'Llamada';
                            break;
                        case 3:
                            $title = 'Email';
                            break;
                    }
                    $dataChart[] = [
                        'title' => $title,
                        'datos' => $contador['sub' . $p + 1],
                    ];
                }
            }
        }else{
            $contador = ['total' => 0,];
            $dataChart = [];
        }

        


        return view('clients/show', compact('client', 'dataEstrategias', 'contador', 'dataEstrategiasNot', 'dataChart'));
        //return view('clients/show', compact('client'));
    }
}
