<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\MailConfig;
use Illuminate\Http\Request;
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


    
    public function index(){



        $config_layout = [
            'title-section' => 'Configuracion > Emails',
            'breads' => 'Configuracion > Emails',
            'btn-create' => 'mail-config.create'
        ];



        $data = MailConfig::all();
        return view('config.mail.index', compact('data', 'config_layout'));
    }

    public function create(){
        $config_layout = [
            'title-section' => 'Emails > Nueva configuracion',
            'breads' => 'Configuracion > Nueva configuracion',
            'btn-back' => 'mail-config.index'
        ];

        $clientes = Http::get(env('API_URL') . env('API_CLIENTS'));
        $data = [];

        foreach($clientes->json()[0] as $cliente){
            $data[$cliente['prefix']] =  $cliente['name'];
        }


        return view('config.mail.create', compact('config_layout', 'data'));
    }


    public function store(Request $request){

        // return $request;
        $saveConfig = new MailConfig();

        $file = $request->file('template');
        $fileName = $file->getClientOriginalName();
        $location = 'uploads/templates';
        $file->move($location,$fileName);
        $saveConfig->name = $request->name;
        $saveConfig->emailfrom = $request->emailfrom;
        $saveConfig->nombrefrom = $request->nombrefrom;
        $saveConfig->asunto = $request->asunto;
        $saveConfig->emailReply = $request->emailReply;
        $saveConfig->template_uri = $location.'/'.$fileName;
        $saveConfig->prefix = $request->prefix;
        $saveConfig->type = $request->type;
        
        $saveConfig->save();
        

        return redirect()->route('mail-config.index');
    }
}
