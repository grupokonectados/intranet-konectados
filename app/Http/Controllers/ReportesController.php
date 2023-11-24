<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Config\UserController;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Ramsey\Uuid\v1;

class ReportesController extends Controller
{
    // $bloqueados = \DB::connection('mysql_31')->table('bloqueados')->get();

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
            'title-section' => 'Reportes',
            'breads' => 'Reportes',
        ];

        // return $data;

        return view('reportes.index', compact('data', 'config_layout'));
    }

    public function generate($id)
    {
        $config_layout = [
            'title-section' => 'Reportes',
            'breads' => 'Reportes',
            'btn-back' => 'reports.index'
        ];

        return view('reportes.generate', compact('config_layout', 'id'));

        // return $data;
    }

    public function csv(Request $request)
    {


        $result_rc = $this->getRegistroContacto($request->client_id, $request->ch, $request->fecha);
        $result_ac = $this->getAllContacts($request->client_id, $request->ch, $request->fecha);

        $arr = array_merge($result_ac, $result_rc);

        ob_start();

        header('Content-Type: text/csv; charset=utf-8');
        //GestionesDiariasDDMMYYYYKonectados
        if ($request->name != '') {
            header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', $request->name) . date('dmY', strtotime($request->fecha)) . 'Konectados.csv');
        } else {
            header('Content-Disposition: attachment; filename=GestionesDiarias' . date('dmY', strtotime($request->fecha)) . 'Konectados.csv');
        }

        $header_args = [
            'ID_EMPRESA_COBRANZA',
            'MES_ASIGNACION',
            'FECHA_GESTION',
            'HORA_ACCION',
            'ID_CAMPANA',
            'ID_SEGMENTO',
            'ID_GRUPO_CONTROL',
            'RUT',
            'CONTRATO',
            'ID_ACCION',
            'ID_NIVEL_UNO',
            'ID_NIVEL_DOS',
            'ID_NIVEL_TRES',
            'CLASIFICACION',
            'OBSERVACION',
            'FECHA_FUTURA',
            'FONO',
            'EMAIL',
            'COBRADOR',
        ];

        ob_end_clean();

        $output = fopen('php://output', 'w');

        fputcsv($output, $header_args, ";", '"', "\\", PHP_EOL);
        foreach ($arr as $data_item) {
            $row = implode(';', $data_item) . "\r\n";
            fwrite($output, $row);
        }

        fclose($output);
        exit;
    }

    public function getRegistroContacto($client_id, $ch, $fecha)
    {
        $conn = DB::connection('mysql_' . $client_id);
        $modo = "";
        if (count($ch) > 1 && ($ch[0] == 'BOT' || $ch[0] == 'AGENTE') || $ch[0] != 'todos') {
            $modo = "AND modo IN (";
            foreach ($ch as $index => $value) {
                if ($value == 'AGENTE') {
                    if ($index < count($ch) - 1) {
                        $modo .= ', ';
                        $modo .= "'INBOUND', 'OUTBOUND', 'MANUAL'";
                    } else {
                        $modo .= "'INBOUND', 'OUTBOUND', 'MANUAL'";
                    }
                } elseif ($value == 'BOT') {
                    if ($index < count($ch) - 1) {

                        $modo .= "'$value'";
                        $modo .= ', ';
                    } else {
                        $modo .= "'$value'";
                    }
                }
            }
            $modo .= ')';
        }

        $query = "SELECT rc.rut, rc.telefono, rc.nopago, rc.idrespuesta, rc.modo, rc.fecha, rc.feccomp, c.IDEmpresaCobranza, c.MesAsignacion, c.Segmento, c.IDCampana, c.IDGrupoControl, c.Contrato FROM (SELECT * FROM registro_contacto WHERE date(fecha) = '$fecha' AND telefono != 'MANUAL'";
        $query .= $modo != '' ? $modo . ' ' : '';
        $query .= ") rc INNER JOIN (SELECT c1.rutsd, MAX(c1.IDEmpresaCobranza) AS IDEmpresaCobranza, MAX(c1.MesAsignacion) AS MesAsignacion, MAX(c1.Segmento) AS Segmento, MAX(c1.IDCampana) AS IDCampana, MAX(c1.IDGrupoControl) AS IDGrupoControl, MAX(c1.Contrato) AS Contrato 
                                    FROM bdcl$client_id.cartera_primer_dia c1 GROUP BY c1.RUTSD) AS c ON rc.rut = c.rutsd";
        // return $query;

        $rst = $conn->select($query);

        $arr_rc = [];

        foreach ($rst as $key_rc => $row_rc) {
            if (preg_match('/^\d{9}$/', $row_rc->telefono)) {
                if ($row_rc->idrespuesta != '555') {
                    $ID_ACCION = '';
                    $ID_NIVEL_UNO = '';
                    $ID_NIVEL_DOS = '';
                    $ID_NIVEL_TRES = '';
                    $CLASIFICACION = '';
                    $OBSERVACION = '';
                    $COBRADOR = '';


                    switch ($row_rc->modo) {
                        case 'INBOUND':
                            $COBRADOR = '106';
                            $ID_ACCION = '008';
                            break;
                        case 'OUTBOUND':
                            $COBRADOR = '106';
                            $ID_ACCION = '009';
                            break;
                        case 'MANUAL':
                            $COBRADOR = '106';
                            $ID_ACCION = '009';
                            break;
                        case 'BOT':
                            $COBRADOR = '108';
                            $ID_ACCION = '001';
                            break;
                    }

                    $CLASIFICACION =  $row_rc->nopago;
                    $OBSERVACION =  $row_rc->idrespuesta;



                    if (
                        ($row_rc->idrespuesta == '050' ||
                            $row_rc->idrespuesta == '051' ||
                            $row_rc->idrespuesta == '052' ||
                            $row_rc->idrespuesta == '053' ||
                            $row_rc->idrespuesta == '054' ||
                            $row_rc->idrespuesta == '055' ||
                            $row_rc->idrespuesta == '056' ||
                            $row_rc->idrespuesta == '057' ||
                            $row_rc->idrespuesta == '059' ||
                            $row_rc->idrespuesta == '060' ||
                            $row_rc->idrespuesta == '061' ||
                            $row_rc->idrespuesta == '063' ||
                            $row_rc->idrespuesta == '064'
                        ) &&
                        ($row_rc->nopago == '095' ||
                            $row_rc->nopago == '086' ||
                            $row_rc->nopago == '087' ||
                            $row_rc->nopago == '088' ||
                            $row_rc->nopago == '089' ||
                            $row_rc->nopago == '090' ||
                            $row_rc->nopago == '092' ||
                            $row_rc->nopago == '093' ||
                            $row_rc->nopago == '094')
                    ) {

                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '023';
                        $ID_NIVEL_TRES = '036';
                        if ($ID_ACCION == '001') {
                            $CLASIFICACION = '';
                        }
                    } elseif ($row_rc->idrespuesta == '058' && ($ID_ACCION != '001')) {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '022';
                        $ID_NIVEL_TRES = '037';
                        $CLASIFICACION = '';
                    } elseif ($row_rc->idrespuesta == '062' || $row_rc->idrespuesta == '065' || $row_rc->idrespuesta == '066') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '022';
                        $ID_NIVEL_TRES = '039';
                        $CLASIFICACION = '';
                    }


                    if ($row_rc->idrespuesta == '090' && ($ID_ACCION != '009') || ($ID_ACCION == '001' && $row_rc->idrespuesta == '058')) {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '038';
                        if ($ID_ACCION == '001') {
                            $CLASIFICACION = '';
                            $OBSERVACION = '';
                        }
                    } elseif ($row_rc->idrespuesta == '051' && ($ID_ACCION != '009')) {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '024';
                        $ID_NIVEL_TRES = '038';
                        if ($ID_ACCION == '001') {
                            $CLASIFICACION = '';
                            $OBSERVACION = '';
                        }
                    } elseif (($row_rc->idrespuesta == '053' || $row_rc->idrespuesta == '050') && ($ID_ACCION != '009' && $ID_ACCION != '008')) {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '027';
                        $ID_NIVEL_TRES = '036';
                        if ($ID_ACCION == '001') {
                            $CLASIFICACION = '';
                            $OBSERVACION = '';
                        }
                    } elseif ($row_rc->idrespuesta == '058' && ($ID_ACCION != '009' && $ID_ACCION != '001')) {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '027';
                        $ID_NIVEL_TRES = '037';
                    }




                    if ($ID_NIVEL_UNO != '') {
                        $arr_rc[$key_rc] = [
                            'ID_EMPRESA_COBRANZA' => $row_rc->IDEmpresaCobranza,
                            'MES_ASIGNACION' => date('Y') . ($row_rc->MesAsignacion < 10 ? '0' . $row_rc->MesAsignacion : $row_rc->MesAsignacion),
                            'FECHA_GESTION' => date('d-m-Y', strtotime($row_rc->fecha)),
                            'HORA_ACCION' => date('H:i', strtotime($row_rc->fecha)),
                            'ID_CAMPANA' => $row_rc->IDCampana,
                            'ID_SEGMENTO' => $row_rc->Segmento == 'J' ? '151' : '150',
                            'ID_GRUPO_CONTROL' => $row_rc->IDGrupoControl,
                            'RUT' =>  $row_rc->rut,
                            'CONTRATO' => (trim($row_rc->Contrato, " ")),
                            'ID_ACCION' => $ID_ACCION,
                            'ID_NIVEL_UNO' => $ID_NIVEL_UNO,
                            'ID_NIVEL_DOS' => $ID_NIVEL_DOS,
                            'ID_NIVEL_TRES' => $ID_NIVEL_TRES,
                            'CLASIFICACION' => $CLASIFICACION,
                            'OBSERVACION' => $OBSERVACION,
                            'FECHA_FUTURA' => $row_rc->feccomp,
                            'FONO' => '56' . $row_rc->telefono,
                            'EMAIL' => '',
                            'COBRADOR' => $COBRADOR,
                        ];
                    }
                }
            }
        }

        return $arr_rc;
    }

    public function getAllContacts($client_id, $ch, $fecha)
    {
        $conn = DB::connection('mysql_' . $client_id);
        $modo = "";
        // return $ch;
        if (count($ch) > 1 && ($ch[0] == 'EM' || $ch[0] == 'AU' || $ch[0] == 'SM' || $ch[0] == 'AGENTE') || $ch[0] != 'todos') {
            // return $ch;
            $modo = "AND tipo IN (";
            foreach ($ch as $index => $value) {
                if ($value == 'EM') {
                    if ($index < count($ch) - 1) {

                        $modo .= "'$value'";
                        $modo .= ', ';
                    } else {
                        $modo .= "'$value'";
                    }
                } elseif ($value == 'AU') {
                    if ($index < count($ch) - 1) {

                        $modo .= "'$value'";
                        $modo .= ', ';
                    } else {
                        $modo .= "'$value'";
                    }
                } elseif ($value == 'SM') {
                    if ($index < count($ch) - 1) {

                        $modo .= "'$value'";
                        $modo .= ', ';
                    } else {
                        $modo .= "'$value'";
                    }
                } elseif ($value == 'AGENTE') {
                    if ($index < count($ch) - 1) {

                        $modo .= "'VI'";
                        $modo .= ', ';
                    } else {
                        $modo .= "'VI'";
                    }
                }
            }
            $modo .= ')';
        }


        $query = "SELECT
                        ac.rut, ac.tipo, ac.respuesta, ac.fecha, ac.feccomp, ac.telefono, ac.glosa,
                        c.IDEmpresaCobranza, c.MesAsignacion, c.Segmento, c.IDCampana, c.IDGrupoControl, c.Contrato
                        FROM (SELECT * FROM bdcl$client_id.all_contacts WHERE date(fecha)='$fecha'";

        $query .= $modo != '' ? $modo : "AND tipo IN ('AU', 'EM', 'SM', 'VI')";

        $query .= ") ac
                        INNER JOIN (
                            SELECT
                                c1.rutsd,
                                MAX(c1.IDEmpresaCobranza) AS IDEmpresaCobranza,
                                MAX(c1.MesAsignacion) AS MesAsignacion,
                                MAX(c1.Segmento) AS Segmento,
                                MAX(c1.IDCampana) AS IDCampana,
                                MAX(c1.IDGrupoControl) AS IDGrupoControl,
                                MAX(c1.Contrato) AS Contrato
                            FROM
                                bdcl$client_id.cartera_primer_dia c1
                            GROUP BY c1.RUTSD
                        ) AS c ON ac.rut = c.rutsd";

        // return $query;

        $rst = $conn->select($query);

        $arr = [];





        foreach ($rst as $key => $row) {

            if ($row->telefono == '' || $row->telefono == null || preg_match('/^\d{9}$/', $row->telefono)) {

                $ID_ACCION = '';
                $ID_NIVEL_UNO = '';
                $ID_NIVEL_DOS = '';
                $ID_NIVEL_TRES = '';
                $CLASIFICACION = '';
                $OBSERVACION = '';
                $COBRADOR = '';

                if ($row->tipo == 'AU') { // VALIDAMOS EL TIPO IVR
                    $ID_ACCION = "003";
                    $COBRADOR = '108';

                    if ($row->respuesta == '32 SC BUZON' || $row->respuesta == '30 SC OCUPADO' || $row->respuesta == 'Outbound Pre-Routing Drop' || $row->respuesta == '24 CI AGENTE NO DISPONIBLE' || $row->respuesta == '35 SC LLAMADA TERMINADA') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '038';
                    } elseif ($row->respuesta == '34 SC NUMERO NO EXISTE') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '037';
                    } elseif ($row->respuesta == 'Call Transferred') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '029';
                        $ID_NIVEL_TRES = '036';
                    } else {
                        $ID_NIVEL_UNO = $row->respuesta;
                    }
                } elseif ($row->tipo == 'VI') { // VALIDAMOS EL TIPO DISCADOR
                    $ID_ACCION = '009';
                    $ID_NIVEL_UNO = '017';
                    $ID_NIVEL_DOS = '021';
                    $ID_NIVEL_TRES = '038';
                    $COBRADOR = '107';
                } elseif ($row->tipo == 'EM') { // VALIDAMOS EL TIPO EMAIL
                    $ID_ACCION = '005';
                    $COBRADOR = '108';

                    if ($row->respuesta == 'SENT') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '024';
                        $ID_NIVEL_TRES = '038';
                    } elseif ($row->respuesta == 'DELIVERED') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '025';
                        $ID_NIVEL_TRES = '036';
                    } elseif ($row->respuesta == 'LEIDO' && $ID_ACCION != '009') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '027';
                        $ID_NIVEL_TRES = '036';
                    } elseif ($row->respuesta == 'BOUNCE' && $ID_ACCION != '009') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '038';
                    } elseif ($row->respuesta == 'INVALID ADDRESS') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '037';
                    }
                }


                $arr[$key] = [
                    'ID_EMPRESA_COBRANZA' => $row->IDEmpresaCobranza,
                    'MES_ASIGNACION' => date('Y') . ($row->MesAsignacion < 10 ? '0' . $row->MesAsignacion : $row->MesAsignacion),
                    'FECHA_GESTION' => date('d-m-Y', strtotime($row->fecha)),
                    'HORA_ACCION' => date('H:i', strtotime($row->fecha)),
                    'ID_CAMPANA' => $row->IDCampana,
                    'ID_SEGMENTO' => $row->Segmento == 'J' ? '151' : '150',
                    'ID_GRUPO_CONTROL' => $row->IDGrupoControl,
                    'RUT' =>  $row->rut,
                    'CONTRATO' => (trim($row->Contrato, " ")),
                    'ID_ACCION' => $ID_ACCION,
                    'ID_NIVEL_UNO' => $ID_NIVEL_UNO,
                    'ID_NIVEL_DOS' => $ID_NIVEL_DOS,
                    'ID_NIVEL_TRES' => $ID_NIVEL_TRES,
                    'CLASIFICACION' => $CLASIFICACION != '' ?  $CLASIFICACION  : '',
                    'OBSERVACION' => $OBSERVACION,
                    'FECHA_FUTURA' => $row->feccomp,
                    'FONO' => ($row->tipo != 'EM' ? '56' . $row->telefono : ''),
                    'EMAIL' => $row->tipo == 'EM' ? $row->glosa : '',
                    'COBRADOR' => $COBRADOR,
                ];
            }
        }
        return $arr;
    }
}
