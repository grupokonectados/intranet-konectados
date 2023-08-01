<?php

namespace App\Http\Controllers;

use App\Models\Config\MailConfig;
use App\Models\MailNotify;
use Illuminate\Http\Request;

class MailNotifyController extends Controller
{



    public function send_notify($arr_data)
    {
        $prefix = $arr_data['prefix'];

        unset($arr_data['prefix']);


        // 1. Buscamos la configuracion del cliente
        $config_mail = MailConfig::find(4);

        // Config del correo 

        $idCamp = "GK0001";
        $emailFrom = "autopase@email.grupokonectados.com";
        $nombreFrom = "Autopase";
        $bodyHtml = $config_mail->body_template;
        $asunto = "TEST - Aviso de vencimiento";
        $emailReply = "autopase@grupokonectados.com";




        // 2. Guardamos los registros en la DB
        foreach ($arr_data as $data) {
            $notify = new MailNotify();
            $notify->prefix = $prefix;
            $notify->rut = $data[0];
            // $notify->nombre = $data[1];
            $notify->save();

            // return 

            $codMsg = $notify->id;

            $request_param = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <Ingreso xmlns="http://WsArchivos.Puntonet.cl/">
                    <Usuario>' . env('USER_WS') . '</Usuario>
                    <Pass>' . env('PASSWORD_WS') . '</Pass>
                    <Cod_Mensaje>' .  $codMsg. '</Cod_Mensaje>
                    <Id_Campana>' . $idCamp . '</Id_Campana>
                    <Email>' . $data[2] . '</Email>
                    <Email_From>' . $emailFrom . '</Email_From>
                    <Nombre_From>' . $nombreFrom . '</Nombre_From>
                    <Cuerpo>' . $bodyHtml . '</Cuerpo>
                    <Asunto>' . $asunto . '</Asunto>
                    <Reply>' . $emailReply . '</Reply>
                    </Ingreso>
                </soap:Body>
            </soap:Envelope>';


            $headers = [
                'Content-Type: text/xml; charset=utf-8',
                'Content-Length: ' . strlen($request_param)
            ];



            $ch = curl_init(env('URL_WS'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_param);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $data = curl_exec($ch);
            $result = $data;
            if ($result === FALSE) {
                printf(
                    "CURL error (#%d): %s<br>\n",
                    curl_errno($ch),
                    htmlspecialchars(curl_error($ch))
                );
            }

            curl_close($ch);

            $xml  = simplexml_load_string($result);
            $ingreso = $xml->xpath("//soap:Body/*")[0];

            MailNotify::where('id', $codMsg)->update(['estado'=> $ingreso->IngresoResult]);

        }



        return MailNotify::all();
    }
}
