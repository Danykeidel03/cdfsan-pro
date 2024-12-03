<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Masterminds\HTML5\Exception;

class addJugadores extends Controller
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
            case "insertarJugador":
                return $this->insertarJugador($request->input('nombre'),$request->input('apellidos'),$request->input('fechaNac'),$request->input('equipo'));
            case "mostarEquipos":
                return $this->mostarEquipos();
            case "mostarPlayers":
                return $this->mostarJugadores($request->input('equipo'));
            default:
                return $this->consultar();
        }
    }

    public function insertarJugador($nombreAdd,$apellidosAdd,$fechaNacAdd,$equipoAdd){

        $this->grabarLog('entra aqui');

        $insertado = DB::table('jugadores')->insert([
            'nombre' => $nombreAdd,
            'apellidos' => $apellidosAdd,
            'fechaNac' => $fechaNacAdd,
            'equipo' => $equipoAdd,
            'minutos' => 0,
            'partidos' => 0,
            'goles' => 0,
            'faltasTotales' => 0
        ]);

        if ($insertado) {
            $this->grabarLog('insertado');
        }

        return $this->respuestaController('accion', array());
    }


    /**
     * Consulta el contador de $equipos creadas en glosarios
     * @param Request $request
     * @return false|string Respuesta
     */
    public function mostarEquipos()
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

    /**
     * Consulta el contador de $equipos creadas en glosarios
     * @param Request $request
     * @return false|string Respuesta
     */
    public function mostarJugadores($equipo)    {
        try {
            $jugadoresFinal = [];
            if($equipo == "null"){
                $jugadoresFinal = DB::table('jugadores')
                    ->select('id_jugador','nombre','apellidos', 'equipo', 'fechaNac')
                    ->get();
            }else{

                $nombreEquipo = DB::table('equipos')
                    ->select('categoria')
                    ->where('id_equipo', $equipo)
                    ->get();

                $nombreEquipo = json_decode($nombreEquipo, true);

                $jugadoresFinal = DB::table('jugadores')
                    ->select('id_jugador','nombre','apellidos', 'equipo', 'fechaNac','minutos','partidos','goles')
                    ->where('equipo', $nombreEquipo)
                    ->get();
            }
        } catch (Exception $e) {
        }
        $result = [
            'info' => 200,
            'resultado' => "OK",
            'error_msg' => "sin error",
            'response' => $jugadoresFinal
        ];
        return $this->respuestaController("bien",$result);
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
        return view('new-jugadores', $arrViewData);
    }


}
