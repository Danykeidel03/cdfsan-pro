<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //
        // Vamos a consultar si tenemos permiso para esta url
//        if ($request->user() == null
//            && explode('/', $request->url())[count(explode('/', $request->url())) - 1] != 'public'
//            && explode('/', $request->url())[count(explode('/', $request->url())) - 1] != 'login'
//        ) {
//            return abort(403, "ÁREA RESTRINGIDA");
//        }
//          $datos = DB::table('usuariosRoles')
//               ->join('rolesFormularios', 'usuariosRoles.idRol', '=', 'rolesFormularios.idRol')
//               ->join('formularios', 'rolesFormularios.idFormulario', '=', 'formularios.id')
//               ->whereRaw("usuariosRoles.idUsuario = " . $request->user()->getAuthIdentifier() . " and formularios.clave='". explode('/', $request->url())[count(explode('/', $request->url())) - 1] . "'")
//               ->select("formularios.clave")->get();
//        if (count($datos) == 0) {
//            return abort(403, "ÁREA RESTRINGIDA");
//        }
        return $next($request);
    }
}
