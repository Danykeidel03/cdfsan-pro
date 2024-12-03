<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Masterminds\HTML5\Exception;

class Equipos extends Controller
{
    /**
     * IndexAddPalabras constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Trata que acción hay que realizar y la ejecuta
     * @return false|Application|Factory|View|string
     */
    public function index(Request $request)
    {
        switch ($request->input('accion')) {
            case "mostarEquipos":
                return $this->mostarEquipos($request->input('equipo'));
            default:
                return $this->consultar();
        }
    }

    /**
     * Obtiene los parametros de entrada y devuelve el view del controller
     * @return Application|Factory|View
     */
    private function consultar()
    {
        $ajaxUrl = url()->full();
        $arrViewData = [
            'ajaxUrl' => $ajaxUrl
        ];
        return view('equipos', $arrViewData);
    }

    /**
     * Consulta el contador de $equipos creadas en glosarios
     * @param Request $request
     * @return false|string Respuesta
     */
    public function mostarEquipos(string $equipo)
    {
        try {
            $equipos = DB::table('equipos')
                    ->select('id_equipo','categoria', 'entrenadores')
                    ->get();
        } catch (Exception $e) {
        }
        $result = [
            'info' => 200,
            'resultado' => "OK",
            'error_msg' => "sin error",
            'response' => $equipos
        ];
        return $this->respuestaController("bien",$result);
    }

}
