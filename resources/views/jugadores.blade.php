@extends('adminlte::page')

@section('title', 'CDFSAN')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/cabecera.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jugadores.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

@stop
@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="{{ asset('js/calcEdad.js') }}"></script>
        <script>
            var table;
            var tablaTodos
            $(document).ready(function () {
                const currentUrl = new URL(window.location.href); // Crea una instancia de URL desde la URL actual
                const id_equipo = currentUrl.searchParams.get('id_equipo'); // Obtiene el valor de id_partido
                obtenerInformacionEquipos(id_equipo);
                document.querySelector('.buttonVentana').addEventListener('click', abrirOverlay)
                let overLay = document.querySelector('.overlay')
                overLay.addEventListener('click', abrirOverlay);
                table = $('#jugadores').DataTable({
                    info: false,
                    autoWidth: false,
                    lengthMenu: [[5], [5]],  // Solo 5 filas por página
                    pageLength: 5,  // Establece la cantidad inicial de filas por página en 5
                    oLanguage: {
                        "sSearch": "",
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.12.1/i18n/es-ES.json"
                    },
                    aria: {
                        sortAscending: ": activate to sort column ascending",
                        sortDescending: ": activate to sort column descending"
                    },
                    initComplete: function () {
                        var $buscar = $('.dt-search input');
                        $buscar.attr('placeholder', 'Buscar en palabras');
                    }
                });
                obtenerInformacionJugadoresDisponibles()
                obtenerInformacionJugadores(id_equipo)
                tablaTodos = $('#jugadoresEquipo').DataTable({
                    info: false,
                    autoWidth: false,
                    oLanguage: {
                        "sSearch": "",
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.12.1/i18n/es-ES.json"
                    },
                    aria: {
                        sortAscending: ": activate to sort column ascending",
                        sortDescending: ": activate to sort column descending"
                    },
                    initComplete: function () {
                        var $buscar = $('.dt-search input');
                        $buscar.attr('placeholder', 'Buscar en palabras');
                    }
                });
                obtenerNextMatch(id_equipo, "next")
            });

            /**
             * Obtenemos la informacion del equipo
             */
            function obtenerInformacionEquipos(equipo){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarEquipo");
                    form_data.append("equipo", equipo);

                    fetch("{{ $ajaxUrl }}", {
                        method: 'POST',
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: form_data,
                        cache: 'no-cache',
                    })
                        .then(res => {
                            return res.json();
                        })
                        .then(data => {
                            equipo = data.result.response[0]
                            let tituloEquipo = document.querySelector('.titleJugadores')
                            tituloEquipo.innerHTML = equipo.categoria
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                            console.log('jugadores sacados');
                        });
                });
            }

            function abrirOverlay(){
                let over = document.querySelector('.ventanaModal');
                if (window.getComputedStyle(over).display === 'none') {
                    over.style.display = 'flex';
                } else {
                    over.style.display = 'none';
                }
            }

            /**
             * Obtenemos los jugadores que estan sin equipo
             */
            function obtenerInformacionJugadoresDisponibles(){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarJugadores");

                    for (var value of form_data.values()) {}

                    fetch("{{ $ajaxUrl }}", {
                        method: 'POST',
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: form_data,
                        cache: 'no-cache',
                    })
                        .then(res => {
                            return res.json();
                        })
                        .then(data => {
                            // Asumimos que data.result.response es un array de jugadores
                            let jugadores = data.result.response;
                            const currentUrl = new URL(window.location.href); // Crea una instancia de URL desde la URL actual
                            const id_equipo = currentUrl.searchParams.get('id_equipo'); // Obtiene el valor de id_partido
                            table.clear();
                            jugadores.forEach(jugador => {
                                jugador.nombreCompleto = jugador.nombre + ' ' + jugador.apellidos;
                                if(jugador.equipo === "null" || jugador.equipo === null){
                                    jugador.equipo = "Ninguno"
                                }
                                table.row.add([
                                    jugador.nombreCompleto,
                                    `<button class="btnAddPlayer" onclick="cambioEquipo('${jugador.id_jugador}','${id_equipo}')">+</button>`
                                ]);
                            });
                            table.draw();
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });
            }

            /**
             * Cambiamos a jugaodres de equipo
             */
            function cambioEquipo(idJugador,equipoNuevo){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "cambioEquipo");
                    form_data.append("idJugador", idJugador);
                    form_data.append("equipoNuevo", equipoNuevo);

                    fetch("{{ $ajaxUrl }}", {
                        method: 'POST',
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: form_data,
                        cache: 'no-cache',
                    })
                        .then(res => {
                            return res.json();
                        })
                        .then(data => {

                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                            console.log('jugador asignado');
                        });
                });
            }


            /**
             * Obtenemos la informacion para mostrar en el grid de idiomas
             */
            function obtenerInformacionJugadores(equipo){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarJugadoresEquipo");
                    form_data.append("equipo", equipo);

                    for (var value of form_data.values()) {}

                    fetch("{{ $ajaxUrl }}", {
                        method: 'POST',
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: form_data,
                        cache: 'no-cache',
                    })
                        .then(res => {
                            return res.json();
                        })
                        .then(data => {
                            // Asumimos que data.result.response es un array de jugadores
                            let jugadores = data.result.response;
                            tablaTodos.clear();
                            jugadores.forEach(jugador => {
                                jugador.nombreCompleto = jugador.nombre + ' ' + jugador.apellidos;
                                jugador.edad = calcularEdad(jugador.fechaNac)
                                if(jugador.equipo === "null" || jugador.equipo === null){
                                    jugador.equipo = "Ninguno"
                                }
                                tablaTodos.row.add([
                                    jugador.nombreCompleto,    // Columna 1: Nombre del jugador
                                    jugador.partidos,
                                    jugador.minutos,
                                    jugador.goles,
                                ]);
                            });
                            tablaTodos.draw();
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });
            }

            /**
             * Obtener los partidos
             */
            function obtenerNextMatch(equipoMatch,fecha){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarPartidos");
                    form_data.append("equipo", equipoMatch);
                    form_data.append("fecha", fecha);

                    fetch("{{ $ajaxUrl }}", {
                        method: 'POST',
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: form_data,
                        cache: 'no-cache',
                    })
                        .then(res => {
                            return res.json();
                        })
                        .then(data => {
                            let partidos = data.result.response;
                            const container = document.querySelector('.futurosPartidos');

                            partidos.forEach(partido => {
                                // Crear el nuevo elemento partido
                                let partidoElement = document.createElement('div');
                                partidoElement.classList.add('partido');

                                // Insertar contenido en el nuevo elemento
                                                        partidoElement.innerHTML = `
                              <div class="imgEquipo">
                                <img style="width: 100px; height: auto" src="${partido.escudo}">
                              </div>
                              <div class="datos">
                                <div class="nombre">${partido.visitante}</div>
                                <div class="fecha">${partido.hora}</div>
                                <div class="acciones">Acciones</div>
                              </div>
                            `;

                                // Añadir el nuevo partido al contenedor
                                container.appendChild(partidoElement);

                                // Añadir el evento click
                                partidoElement.addEventListener('click', function() {
                                    let partidoId = partido.id_partido;
                                    let currentUrl = new URL(window.location.origin + window.location.pathname.split('/').slice(0, -2).join('/') + '/public/partido');

                                    if (partidoId !== 'all') {
                                        currentUrl.searchParams.set('id_partido', partidoId);
                                    } else {
                                        currentUrl.searchParams.delete('id_equipo');
                                    }

                                    window.location.href = currentUrl.toString();
                                });
                            });

                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });
            }
        </script>
@endpush
@php

    @endphp
@section('content')
    <div id="panel-datos">
        <h1 class="titleJugadores"></h1>
        <div class="navBar">
            <button class="buttonVentana">Añadir Jugador</button>
            <div class="ventanaModal" style="display: none">
                <div class="overlay"></div>
                <div class="formJugador">
                    <table id="jugadores" class="display" style="width:100%; padding-top: 20px;">
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Añadir</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="todosJugadores" style="margin-top: 50px;">
            <table id="jugadoresEquipo" class="display" style="width:100%; padding-top: 20px;">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Partidos</th>
                    <th>Minutos</th>
                    <th>Goles</th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="nextMatch">
            <h1 class="titleJugadores">Futuros partidos</h1>
            <div class="futurosPartidos"></div>
            <div class="ultimosPartidos"></div>
        </div>
    </div>
@endsection
