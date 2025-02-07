<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Masterminds\HTML5\Exception;

class Partido extends addJugadores
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
            case "mostarJugadoresEquipo":
                return $this->mostarJugadores($request->input('equipo'));
            case "addTitulares":
                return $this->addTitulares($request->input('titulares'),$request->input('suplentes'),$request->input('partido'));
            case "mostarJugadoresEquipoTitular":
                return $this->mostarJugadoresEquipoTitular($request->input('partido'));
            case "mostarEquipoPartido":
                return $this->mostarEquipoPartido($request->input('equipo'));
            case "addGol":
                return $this->addGol($request->input('id_jugador'));
            case "finalizarMatch":
                return $this->finalizarMtach($request->input('id_partido'),$request->input('resultado'));
            case "realizarCambio":
                return $this->realizarCambio($request->input('id_player'),$request->input('idsJugadoresSeleccionados'),$request->input('partido'),$request->input('minutosCambio'));
            default:
                return $this->consultar();
        }
    }

    public function finalizarMtach($idMatch, $resultado){
        $suplentes = DB::table('minutos')
            ->select('id_jugador', 'minutos')
            ->where('id_partido', $idMatch)
            ->where('titular', 0)
            ->get();

            foreach ($suplentes as $suplente) {
                $idJugador = $suplente->id_jugador;
                $minutosFinales = $suplente->minutos;

                $minutosActuales = DB::table('jugadores')
                    ->select('minutos','partidos')
                    ->where('id_jugador', $idJugador)
                    ->get();

                $arrayJugador = json_decode($minutosActuales,true);
                $jugadorASumar = $arrayJugador[0]['minutos'];
                $jugadorpartidos = $arrayJugador[0]['partidos'];

                $sumaMinutos = $jugadorASumar + $minutosFinales;
                $sumaPartidos = $jugadorpartidos + 1;

                if($sumaMinutos == 0 ){
                    $sumaPartidos = $jugadorpartidos;
                }

                DB::table('jugadores')
                    ->where('id_jugador', '=', $idJugador)
                    ->update([
                        'minutos' => $sumaMinutos,
                        'partidos' => $sumaPartidos
                    ]);
            }


        $titulares = DB::table('minutos')
            ->select('id_jugador')
            ->where('id_partido', $idMatch)
            ->where('titular', 1)
            ->get();

        foreach ($titulares as $titular) {
            $idJugadorTitular = $titular->id_jugador;

            $minutosActualesTitular = DB::table('jugadores')
                ->select('minutos','partidos')
                ->where('id_jugador', $idJugadorTitular)
                ->get();

            $arrayJugadorTitularEntra = DB::table('minutos')
                ->select('minutoEntra','minutos')
                ->where('id_jugador', $idJugadorTitular)
                ->get();

            $arrayJugadorTitular = json_decode($minutosActualesTitular,true);
            $jugadorASumarTitular = $arrayJugadorTitular[0]['minutos'];
            $jugadorpartidosTitular = $arrayJugadorTitular[0]['partidos'];

            $jugadorpartidosTitularSumar = $jugadorpartidosTitular + 1;

            $arrayJugadorTitularEntraFin = json_decode($arrayJugadorTitularEntra,true);
            $jugadorASumarTitularEntra = $arrayJugadorTitularEntraFin[0]['minutoEntra'];
            $jugadorASumarTitularEntraSum = $arrayJugadorTitularEntraFin[0]['minutos'];

            $minutosSumar = 60 - $jugadorASumarTitularEntra;

            $sumaMinutosTitular = $jugadorASumarTitularEntraSum + $minutosSumar;

            $sumaMinutosTitular = $sumaMinutosTitular + $jugadorASumarTitular;

            if($sumaMinutosTitular == 0 ){
                $jugadorpartidosTitularSumar = $jugadorpartidosTitular;
            }

            DB::table('jugadores')
                ->where('id_jugador', '=', $idJugadorTitular)
                ->update([
                    'minutos' => $sumaMinutosTitular,
                    'partidos' => $jugadorpartidosTitularSumar
                ]);

        }


        //falta acabar el partido como tal

        $deleteMinutos = DB::table('minutos')
                ->where('id_partido', $idMatch)
                ->delete() > 0;

        DB::table('partidos')
            ->where('id_partido', '=', $idMatch)
            ->update([
                'finalizado' => 1,
                'resultado' => $resultado
            ]);

    }

    public function addGol($idPlayer){
        $jugadoresFinal = DB::table('jugadores')
            ->select('goles')
            ->where('id_jugador', $idPlayer)
            ->get();

        $arrayJugador = json_decode($jugadoresFinal,true);
        $jugadorGoleador = $arrayJugador[0]['goles'];

        $jugadorGoleador++;

        DB::table('jugadores')
            ->where('id_jugador', '=', $idPlayer)
            ->update([
                'goles' => $jugadorGoleador
            ]);
    }

    public function addTitulares($titulares, $suplentes, $partido){

        $titularesArray = explode(',', $titulares);
        foreach ($titularesArray as $titular) {

            $nombre = DB::table('jugadores')
                ->select('nombre')
                ->where('id_jugador', $titular)
                ->get();

            $nombreFin = json_decode($nombre,true);

            $insertadoTitular = DB::table('minutos')->insert([
                'id_jugador' => $titular,
                'id_partido' => $partido,
                'minutos' => 0,
                'titular' => 1,
                'nombre' => $nombreFin[0]['nombre'],
                'ultimoCambio' => 0,
                'minutoEntra' => 0
            ]);
        }

        $suplentesArray = explode(',', $suplentes);
        foreach ($suplentesArray as $suplente) {

            $nombre = DB::table('jugadores')
                ->select('nombre')
                ->where('id_jugador', $suplente)
                ->get();

            $nombreFin = json_decode($nombre,true);

            $insertadoSuplente = DB::table('minutos')->insert([
                'id_jugador' => $suplente,
                'id_partido' => $partido,
                'minutos' => 0,
                'titular' => 0,
                'nombre' => $nombreFin[0]['nombre'],
                'ultimoCambio' => 0,
                'minutoEntra' => null
            ]);
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
        return view('partido', $arrViewData);
    }

    public function mostarJugadores($equipo)
    {
        return parent::mostarJugadores($equipo); // TODO: Change the autogenerated stub
    }

    private function mostarEquipoPartido($idMatch){
        try {
            $partidos = DB::table('partidos')
                ->select('local','visitante','dia', 'hora', 'id_partido','escudo','id_equipoSan')
                ->where('id_partido', $idMatch)
                ->get();
        } catch (Exception $e) {
        }
        $result = [
            'info' => 200,
            'resultado' => "OK",
            'error_msg' => "sin error",
            'response' => $partidos
        ];
        return $this->respuestaController("bien",$result);
    }

    private function  mostarJugadoresEquipoTitular($idMatch){
        try {
            $minutos = DB::table('minutos')
                ->select('id_minuto','id_jugador','id_partido', 'minutos', 'titular','nombre')
                ->where('id_partido', $idMatch)
                ->get();

        } catch (Exception $e) {
        }
        $result = [
            'info' => 200,
            'resultado' => "OK",
            'error_msg' => "sin error",
            'response' => $minutos
        ];
        return $this->respuestaController("bien",$result);
    }

    private function realizarCambio($idJugadorTitular,$idJugadorSuplente,$idMatch,$minutos){

    $minutosTotales = DB::table('minutos')
        ->select('minutos','minutoEntra')
        ->where('id_partido', $idMatch)
        ->where('id_jugador', '=', $idJugadorTitular)
        ->first();

    $minutosTotalesCambio = DB::table('minutos')
        ->select('ultimoCambio','minutoEntra')
        ->where('id_partido', $idMatch)
        ->where('id_jugador', '=', $idJugadorSuplente)
        ->first();

    if ($minutosTotales) {
        $CambioFin = $minutosTotalesCambio->ultimoCambio;
        $minutosFin = $minutosTotales->minutos;


        if ($CambioFin !== 0 && $minutosTotalesCambio->minutoEntra !== 0) {
            $minutosJugados = $minutos - $minutosTotales->minutoEntra;
            $minutosAdd = $minutosFin + $minutosJugados;
//            $this->grabarLog("en el if: ". $minutosAdd);
        } else {
            $minutosTotalesSiEsCero = DB::table('minutos')
                ->select('minutoEntra', 'ultimoCambio','minutos')
                ->where('id_partido', $idMatch)
                ->where('id_jugador', '=', $idJugadorTitular)
                ->first();

            if ($minutosTotalesSiEsCero->minutoEntra == 0 && $minutosTotalesSiEsCero->ultimoCambio == 0){
                $minutosAdd = $minutos;
//                $this->grabarLog("en el if del else: ". $minutosAdd);
            }else{
                $minutosAdd = $minutosTotalesSiEsCero->minutos + ($minutos - $minutosTotalesSiEsCero->minutoEntra);
//                $this->grabarLog("en el else del else: ". $minutosAdd);
            }
        }

        DB::table('minutos')
            ->where('id_jugador', '=', $idJugadorTitular)
            ->where('id_partido', '=', $idMatch)
            ->update([
                'minutos' => $minutosAdd
            ]);

        DB::table('minutos')
            ->where('id_jugador', '=', $idJugadorTitular)
            ->where('id_partido', '=', $idMatch)
            ->update([
                'ultimoCambio' => $minutos
            ]);
    }



        DB::table('minutos')
            ->where('id_jugador', '=', $idJugadorSuplente)
            ->where('id_partido', '=', $idMatch)
            ->update([
                'titular' => 1,
                'minutoEntra' => $minutos
            ]);

        DB::table('minutos')
            ->where('id_jugador', '=', $idJugadorTitular)
            ->where('id_partido', '=', $idMatch)
            ->update([
                'titular' => 0
            ]);

    }
}
