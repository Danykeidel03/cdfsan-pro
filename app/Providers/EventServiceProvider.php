<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
//            //
//            // Vamos a consultar los formularios que tiene este usuario activados
//            $datos = DB::table('usuariosRoles')
//                ->join('rolesFormularios', 'usuariosRoles.idRol', '=', 'rolesFormularios.idRol')
//                ->join('formularios', 'rolesFormularios.idFormulario', '=', 'formularios.id')
//                ->whereRaw("usuariosRoles.idUsuario = " . Auth::user()->getAuthIdentifier())->select("formularios.clave")->get();
//
//            $itemsMenu = DB::table('formularios')->select("clave")->get();
//
//            $permisos = array();
//
//            foreach ($datos as $dato){
//                $permisos[] = $dato->clave;
//            }
//
//            try {
//                //
//                // Recorremos los items del menu declarados
//                for ($i = 0; $i < count($itemsMenu); $i++) {
//                    //
//                    // Si el formulario no esta dentro de los que
//                    // tiene permiso el usuario lo borramos
//                    if (!in_array($itemsMenu[$i]->clave, $permisos)) {
//                        $event->menu->remove($itemsMenu[$i]->clave);
//                    }
//                }
//            } catch (\Exception $ex) {
//
//            }
        });
    }

}
