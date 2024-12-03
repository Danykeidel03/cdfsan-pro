<?php

namespace App\Http\Controllers;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use function json_encode;
use function strtotime;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->input('accion') == "actualizar") {
            return $this->consultar($request, false, $request->input('check-acumulativo'));
        } else {
            return $this->consultar($request, true, 0);
        }
    }

    /**
     * Consulta los datos para los informes
     * @param $request
     * @param $primera
     * @param $acumulativo
     * @return false|Application|Factory|View|string
     * @throws Exception
     */
    private function consultar($request, $primera, $acumulativo)
    {


        if ($primera) {
            $desde = date('Y-m-d', strtotime('today -7 day'));
            $hasta = date('Y-m-d', strtotime('today'));
        } else {
            $desde = date('Y-m-d', strtotime($request->input('desde')));
            $hasta = date('Y-m-d', strtotime($request->input('hasta')));
        }


        $ajaxUrl = url()->full();
        if ($primera) {
            $arrViewData = [
                'desde' => $desde,
                'hasta' => $hasta,
                'ajaxUrl' => $ajaxUrl
            ];
            return view('home', $arrViewData);
        } else {
//            $stdObjResponse = new stdClass();
//            $stdObjResponse->success = true;
//            $stdObjResponse->qParams = [
//                'Mostrar_exportar' => 0,
//                'accion' => 'actualizar'
//            ];
//            $stdObjResponse->result = [
//                'desde' => $desde,
//                'hasta' => $hasta,
//                'ticketsVentasHoy' => count($datosHoy) == 0 ? 0 : $datosHoy[0]->numIngresos,
//                'ticketsDevolucionesHoy' => count($datosHoy) == 0 ? 0 : $datosHoy[0]->numDevoluciones,
//                'dineroVentasHoy' => count($datosHoy) == 0 ? 0.00 : round($datosHoy[0]->ventas, 2),
//                'dineroDevolucionesHoy' => count($datosHoy) == 0 ? 0.00 : round($datosHoy[0]->devoluciones, 2),
//                'horizontalHoras' => $horizontalHoras,
//                'verticalHoyHoras' => $verticalHoy,
//                'verticalAyerHoras' => $verticalAyer,
//                'verticalSemanaAntesHoras' => $verticalSemanaAntes,
//                'verticalHoyDineroHoras' => $verticalDineroHoy,
//                'verticalAyerDineroHoras' => $verticalDineroAyer,
//                'verticalSemanaAntesDineroHoras' => $verticalDineroSemanaAntes,
//                'horizontalVentasDineroTotal' => $horizontalVentasDineroTotal,
//                'horizontalVentasTotal' => $horizontalVentasTotal,
//                'verticalVentasDineroTotal' => $verticalVentasDineroTotal,
//                'verticalVentasTotal' => $verticalVentasTotal,
//                'horizontalDevolucionesDineroTotal' => $horizontalDevolucionesDineroTotal,
//                'horizontalDevolucionesTotal' => $horizontalDevolucionesTotal,
//                'verticalDevolucionesDineroTotal' => $verticalDevolucionesDineroTotal,
//                'verticalDevolucionesTotal' => $verticalDevolucionesTotal,
//                'permiso' => $permisoDatos
//            ];
//            $stdObjResponse->sqls = [];
//            return json_encode($stdObjResponse);
        }
    }


}
