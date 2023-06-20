<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Estrategia;
use App\Models\Estructura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EstrategiaController extends Controller
{

    const PATH_API = '/estrategias';


    public function index()
    {

        /**
         * Metodo laravel
         */



        $data = Estrategia::select('estrategias.*', 'c.name', 'c.id as client_id')
            ->join('clients as c', 'c.prefix', '=', 'estrategias.prefix_client')
            ->get();




        /**
         * Metodo API
         */


        // $response = Http::get(env('API_URL').self::PATH_API);
        // $data = $response->json();

        //return $data;


        return view('estrategias/index', compact('data'));
    }


    public function create()
    {

        $data = Client::all();
        return view('estrategias/create', compact('data'));
    }


    public function saveEstrategia(Request $request)
    {

        //return $request;

        $saveQuery = new Estrategia();
        $saveQuery->query = $request->query_text;
        $saveQuery->onlyWhere = $request->onlyWhere;
        $saveQuery->channels = $request->channels;
        $saveQuery->table_name = $request->table_name;
        $saveQuery->query_description = $request->query_description;
        $saveQuery->prefix_client = $request->prefix;
        $saveQuery->save();

        return redirect()->route('estrategia.index');
    }

    public function show($id)
    {

        $data = Estrategia::find($id);
        return view('estrategias/show', compact('data'));


        // $response = Http::get(env('API_URL').self::PATH_API.'/'.$id);
        // $data = $response->json();

        // return view('estrategias/show', compact('data'));
        return $data;
    }


    public function runQuery(Request $request)
    {

        // return $request; //Verificar que datos llegan. 

        // Aqui se prueba la consulta y se pueden ver los resultados segun el filtro puesto
        $result = \DB::select($request['query']);

        // Extraemos los nombres de las columnas segun la el cliente que se vaya a utilizar        
        $table = Estructura::select('COLUMN_NAME')->where('TABLE_NAME', '=', $request['table_name'])->get();



        if (count($result) > 0) {

            // se cuenta el total de hallados en la consulta anterior
            $counter = \DB::select("select count(*) as counter from " . $request['table_name'] . " where " . $request['where']);

            //Calculos de factibilidad dependiendo del canal *PREGUNTAR ESTE CASO* 
            switch ($request['channel']) {
                case 1:
                    $factibilidad = \DB::select("select count(movil1) as cc from " . $request['table_name'] . " where " . $request['where'] . " and LENGTH(movil1) != 0");
                    break;
                case 2:
                    $factibilidad = \DB::select("select count(fijo1) as cc from " . $request['table_name'] . " where " . $request['where'] . " and LENGTH(fijo1) != 0");
                    break;
                case 3:
                    $factibilidad = \DB::select("select count(email1) as cc from " . $request['table_name'] . " where " . $request['where'] . " and LENGTH(email1) != 0");
                    break;
            }

            //En base al contador total se le resta el contador segun el canal / (cc = counter channels)
            $resto = $counter[0]->counter - $factibilidad[0]->cc;
            // Se calcula el procentaje y se formatea
            // number_format(int or float, numero de decimales, coma para los decimales, punto para los miles)
            $porcentaje = number_format(($counter[0]->counter / 10000) * 100, 2, ',', '.');

            return [
                'contador' => $counter[0]->counter,  // Muestro el contador
                'result' => $result, // El resultado de la consulta 
                'table' => $table, // Los nombres de las columnas
                'resto' => $resto, // la resta entre la factibilidad y el contador
                'porcentaje' => $porcentaje //el procentaje de factibilidad
            ];
        } else {
            return [
                'result' => 0, // El resultado de la consulta 
                'message' => 'No hay nada que mostrar'
            ];
        }

        return $result;
    }


    public function isActive(Request $request){

        $dataCompare = Estrategia::where('isActive', '=', 1)->get();

        // return count($dataCompare);

        if (count($dataCompare) <= 1 && $request->value == 0) {
            $data = ['error' => 'Debe existir al menos de una estrategia activa'];
            return $data;
        }else{
            $data = Estrategia::where('id', '=', $request->id)->update(['isActive' => $request->value]);
            return $data;
        }

        
    }


    public function queryResults($strings_query)
    {

        $query_ruts = [];
        

        foreach ($strings_query as $k => $v) {
            $query_ruts[$k] = \DB::select("select rut from " . $v['table_name'] . " where " . $v['where']);
        }

        if (count($strings_query) == 3) {
            
            for ($i = 0; $i < count($query_ruts[0]); $i++) {
                $arr1[$i] = $query_ruts[0][$i]->rut;
            }

            for ($o = 0; $o < count($query_ruts[1]); $o++) {
                $arr2[$o] = $query_ruts[1][$o]->rut;
            }

            for ($u = 0; $u < count($query_ruts[2]); $u++) {
                $arr3[$u] = $query_ruts[2][$u]->rut;
            }

            $unico1 = array_diff($arr1, $arr2, $arr3);
            $unico2 = array_diff($arr2, $arr1, $arr3);
            $unico3 = array_diff($arr3, $arr1, $arr2);

            if (count($unico1) <= 0) {
                $unico1 = $arr1;
            }

            if (count($unico2) <= 0) {
                $unico2 = $arr2;
            }

            if (count($unico3) <= 0) {
                $unico3 = $arr3;
            }


            return [
                
                $unico1,
                $unico2,
                $unico3,
                'sub1' =>count($unico1),
                'sub2' =>count($unico2),
                'sub3' =>count($unico3),
                'total' => count($unico1)+count($unico2)+count($unico3),
                
            ];
        }elseif(count($strings_query) == 2){

            for ($i = 0; $i < count($query_ruts[0]); $i++) {
                $arr1[$i] = $query_ruts[0][$i]->rut;
            }

            for ($o = 0; $o < count($query_ruts[1]); $o++) {
                $arr2[$o] = $query_ruts[1][$o]->rut;
            }

            $unico1 = array_diff($arr1, $arr2);
            $unico2 = array_diff($arr2, $arr1);

            if (count($unico1) <= 0) {
                $unico1 = $arr1;
            }

            if (count($unico2) <= 0) {
                $unico2 = $arr2;
            }

            return [
                $unico1,
                $unico2,
                'sub1' =>count($unico1),
                'sub2' =>count($unico2),
                'total' => count($unico1)+count($unico2),
            ];

        }else{
            
            for ($i = 0; $i < count($query_ruts[0]); $i++) {
                $arr1[$i] = $query_ruts[0][$i]->rut;
            }
            
            if (count($arr1) > 0) {
                return [
                    $arr1,
                    'total' => count($arr1),
                    'sub1' =>count($arr1),
                ];
            }
            
        }
    }
}
