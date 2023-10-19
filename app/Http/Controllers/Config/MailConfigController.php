<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\ClientController;
use App\Http\Controllers\Controller;
use App\Models\Config\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MailConfigController extends Controller
{


    function __construct()
    {
        $this->middleware('permission:root-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:root-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:root-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:root-delete', ['only' => ['destroy']]);
    }



    public function index(Request $request)
    {

        $config_layout = [
            'title-section' => 'Configuracion > Emails',
            'breads' => 'Configuracion > Emails',
            'btn-create' => 'mail-config.create',
            'btn-back' => 'clients.show'
        ];
        $getConfigMails = Http::get(env('API_URL') . env('API_EMAILS') . $request->prefix);
        $data = $getConfigMails->json()[0];
        $id_cliente = Cache::get('cliente')->id;
        return view('config.mail.index', compact('data', 'config_layout', 'request', 'id_cliente'));
    }

    public function create(Request $request)
    {
        $config_layout = [
            'title-section' => 'Emails > Nueva configuracion',
            'breads' => 'Configuracion > Nueva configuracion',
            'btn-back' => 'mail-config.index'
        ];
        $columnas = ['@NOMBRE@', '@MONTO@'];
        $columnas_calculadas = ['@FECHA@'];
        $data['prefix'] = $request->prefix;

        // return $data;

        return view('config.mail.create', compact('config_layout', 'data', 'columnas', 'columnas_calculadas'));
    }

    public function store(Request $request)
    {
        $saveConfig = [
            'nombreTemplate' =>  $request->nombreTemplate,
            'prefix' => $request->prefix,
            'columnas' => json_encode($request->columnas),
            'body' => (file_get_contents($request->file('template'))),
            'emailFrom' => $request->emailFrom,
            'nombreFrom' => $request->nombreFrom,
            'asunto' => $request->asunto,
            'emailReply' => $request->emailReply,
            'columnasCalc' => json_encode($request->columnas_calculadas),
        ];


        $getConfigMails = Http::post(env('API_URL') . env('API_EMAILS'), $saveConfig);
        $result = $getConfigMails->json();

        if ($result !== 0) {
            return redirect()->route('mail-config.index', 'prefix=' . $request->prefix);
        } else {
            return redirect()->route('mail-config.create', 'prefix=' . $request->prefix)->with('error', 'Se produjo un error al registrar la configuracion.');
        }
    }

    public function show($id)
    {

        $getConfigMail = Http::get(env('API_URL') . env('API_EMAIL') . $id);
        $data = $getConfigMail->collect()[0];
        $data = $data[0];
        $data['columnas'] = json_decode($data['columnas'], true);
        $config_layout = [
            'title-section' => 'Emails > Ver configuracion: ' . $data['nombreTemplate'],
            'breads' => 'Configuracion > Ver configuracion: ' . $data['nombreTemplate'],
            'btn-back' => 'mail-config.index',
        ];



        // return $data;
        return view('config.mail.show', compact('data', 'config_layout'));
    }


    public function edit($id)
    {


        $getConfigMail = Http::get(env('API_URL') . env('API_EMAIL') . $id);
        $data = $getConfigMail->collect()[0];
        $data = $data[0];
        $data['columnas'] = json_decode($data['columnas'], true);

        $config_layout = [
            'title-section' => 'Emails > Ver configuracion: ' . $data['nombreTemplate'],
            'breads' => 'Configuracion > Ver configuracion: ' . $data['nombreTemplate'],
            'btn-back' => 'mail-config.index',
        ];



        $columnas = ['@NOMBRE@', '@MONTO@'];



        $columnas_calculadas = ['@FECHA@'];

        // return $data;

        return view('config.mail.edit', compact('data', 'config_layout', 'columnas', 'columnas_calculadas'));
    }

    public function update(Request $request, $id)
    {

        return $request;
    }
}
