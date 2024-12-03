<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use stdClass;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function grabarLog($texto)
    {
        $fecha = date('Y-m-d H:i:s.') . gettimeofday()['usec'];
        $myfile = fopen("./_" . Route::current()->getName() . "_.txt", "a");
        fwrite($myfile,$fecha.' -> '. $texto . PHP_EOL);
        fclose($myfile);
    }

    /**
     * Realiza una llamada por curl
     * @param $url String Url de la llamada
     * @param $tipo String Tipo de la llamada (GET/POST/PUT)
     * @return bool|string
     */
    protected function llamadaCurl($url, $tipo)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $tipo);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type:application/json",
                "Authorization:Bearer " . env("API_KEY_MAGENTO")
            )
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $resultado = curl_exec($ch);
        curl_close($ch);

        return $resultado;
    }

    /**
     * Crea un objeto de respuesta del controller
     * @param $action String acción que desencadena la llamada
     * @param $resultado array listado de variables del resultado
     * @return false|string Respuesta
     */
    protected function respuestaController(string $action, array $resultado)
    {
        $stdObjResponse = new stdClass();
        $stdObjResponse->success = true;
        $stdObjResponse->qParams = [
            'Mostrar_exportar' => 0,
            'accion' => $action
        ];
        $stdObjResponse->result = $resultado;
        $stdObjResponse->sqls = [];
        return json_encode($stdObjResponse);
    }
}
