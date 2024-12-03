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
            $(document).ready(function () {
                document.querySelector('.buttonVentana').addEventListener('click', abrirOverlay)
                let overLay = document.querySelector('.overlay')
                overLay.addEventListener('click', abrirOverlay);
                obtenerInformacionEquipos()
                obtenerInformacionJugadores()
                table = $('#jugadores').DataTable({
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
            });

            function abrirOverlay(){
                let over = document.querySelector('.ventanaModal');
                if (window.getComputedStyle(over).display === 'none') {
                    over.style.display = 'flex';
                } else {
                    over.style.display = 'none';
                }
            }

            /**
             * Obtenemos la informacion para mostrar en el grid de idiomas
             */
            function obtenerInformacionEquipos(){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarEquipos");

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
                            let equiposJugadores = (data.result.response)
                            let selectEquipo = document.querySelector('#equipoJugador');
                            equiposJugadores.forEach(equipo => {
                                let option = document.createElement('option'); // Crear una nueva opción
                                option.value = equipo.categoria.toLowerCase().replace(/\s+/g, ''); // Establecer el valor de la opción (sin espacios)
                                option.text = equipo.categoria; // Establecer el texto de la opción
                                selectEquipo.appendChild(option); // Agregar la opción al select
                            })
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });
            }

            /**
             * Insertar jugador
             */
            function insertarJugador() {
                Pace.track(function () {

                    let nombreJugador = document.querySelector('#nombreJugador').value;
                    let apellidosJugador = document.querySelector('#apellidosJugador').value;
                    let fechaNacJugador = document.querySelector('#fechaNacJugador').value;
                    let equipoJugador = document.querySelector('#equipoJugador').value;

                    var form_data = new FormData();
                    form_data.append("accion", "insertarJugador");
                    form_data.append("nombre", nombreJugador);
                    form_data.append("apellidos", apellidosJugador);
                    form_data.append("fechaNac", fechaNacJugador);
                    form_data.append("equipo", equipoJugador);

                    for (var value of form_data.values()) {
                        console.log(value);
                    }

                    console.log("{{ csrf_token() }}")

                    fetch("{{ $ajaxUrl }}", {
                        method: 'POST',
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: form_data,
                        cache: 'no-cache',
                    })
                        .then(res => res.json())
                        .then(data => {
                            console.log(data)
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        })
                        .finally(() => {
                            console.log('Palabra insertada y tabla actualizada');
                        });
                });
            }

            /**
             * Obtenemos la informacion para mostrar en el grid de idiomas
             */
            function obtenerInformacionJugadores(){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarPlayers");
                    form_data.append("equipo", "null");

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
                            table.clear();
                            jugadores.forEach(jugador => {
                                jugador.nombreCompleto = jugador.nombre + ' ' + jugador.apellidos;
                                jugador.edad = calcularEdad(jugador.fechaNac)
                                if(jugador.equipo === "null" || jugador.equipo === null){
                                    jugador.equipo = "Ninguno"
                                }
                                table.row.add([
                                    jugador.nombreCompleto,    // Columna 1: Nombre del jugador
                                    jugador.equipo,    // Columna 2: Equipo al que pertenece
                                    jugador.edad       // Columna 3: Edad del jugador
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
        </script>
@endpush
@php

    @endphp
@section('content')
    <div id="panel-datos">
        <h1 class="titleJugadores">Añadir Jugador</h1>
        <div class="navBar">
            <button class="buttonVentana">Añadir Jugador</button>
            <div class="general">
                <div class="containerGeneral">
                    <div class="separator" style="width:100%;float:left;height:30px;"></div>
                    <div class="informacionGlosarios">
                        <div class="divListado">
                            <div class="listaPalabras">
                                <table id="jugadores" class="display" style="width:100%; padding-top: 20px;">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Equipo</th>
                                        <th>Edad</th>
{{--                                        <th>Eliminar</th>--}}
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ventanaModal" style="display: none">
                <div class="overlay"></div>
                <div class="formJugador">
                    <form>
                        <input type="text" id="nombreJugador" placeholder="Nombre del jugador" required>
                        <input type="text" id="apellidosJugador" placeholder="Apellidos del jugador" required>
                        <input type="date" id="fechaNacJugador" required>
                        <select id="equipoJugador" required>
                            <option value="">Seleccione un equipo</option>
                            <option value="0">Sin Equipo</option>
                        </select>
                        <input type='submit' id='addJugadores' name='addJugadores' class='addJugadores'
                               onclick="insertarJugador()">
                    </form>
                </div>
            </div>
        </div>
        <div class="todosJugadores" style="margin-top: 50px;"></div>
    </div>
@endsection
