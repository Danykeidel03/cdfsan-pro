@extends('adminlte::page')

@section('title', 'CDFSAN')

@section('content_header')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
@stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/cabecera.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/equipos.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jugadores.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
@stop
@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script>
            var tablaTodos
            var tablaTitulares
            var tablaSuplentes
            $(document).ready(function () {
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
                tablaTitulares = $('#jugadoresTitulares').DataTable({
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
                tablaSuplentes = $('#jugadoresSuplentes').DataTable({
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
                const currentUrl = new URL(window.location.href); // Crea una instancia de URL desde la URL actual
                const id_partido = currentUrl.searchParams.get('id_partido'); // Obtiene el valor de id_partido
                datosEquipo(id_partido)
                let overLay = document.querySelector('.ventanaModal .overlay')
                let overLayFin = document.querySelector('.ventanaModal.finalizarPartido .overlay')
                let overAbrir = document.querySelector('.ventanaModal');
                let overAbrirTerminar = document.querySelector('.ventanaModal.finalizarPartido');
                let btnIniciarMatch = document.querySelector('.inicioPartido')
                let btnFinalizarMatch = document.querySelector('.terminarPartido')
                overLay.addEventListener('click', () => abrirOverlay(overAbrir));
                overLayFin.addEventListener('click', () => abrirOverlay(overAbrirTerminar));
                btnIniciarMatch.addEventListener('click', () => abrirOverlay(overAbrir));
                btnFinalizarMatch.addEventListener('click', () => abrirOverlay(overAbrirTerminar));
                obtenerInformacionJugadoresTitulares(id_partido)

            });

            function datosEquipo(id_partido){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarEquipoPartido");
                    form_data.append("equipo", id_partido);

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
                            let nombreOtroEquipo = document.querySelector('.otroEquipo .nombre')
                            nombreOtroEquipo.textContent = equipo.visitante

                            let fotoOtroEquipo = document.querySelector('.otroEquipo img')
                            fotoOtroEquipo.setAttribute('src', equipo.escudo);

                            let diaPartido = document.querySelector('.horario .diaPartido')
                            diaPartido.textContent = equipo.dia

                            let horaPartido = document.querySelector('.horario .horaPartido')
                            horaPartido.textContent = equipo.hora

                            obtenerInformacionJugadores(equipo.id_equipoSan)
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                            console.log('jugadores sacados');
                        });
                });
            }

            function abrirOverlay(over){
                if (window.getComputedStyle(over).display === 'none') {
                    over.style.display = 'flex';
                } else {
                    over.style.display = 'none';
                }
            }

            function abrirOverlayCambio(over, clase, match){
                let id_player = clase[0].split('-')[1];
                if (window.getComputedStyle(over).display === 'none') {
                    over.style.display = 'flex';

                    let formJugador = over.querySelector('.formJugador');
                    // Crear el botón solo si no existe
                    if (!document.getElementById('cambioButton')) {
                        let button = document.createElement('button');
                        button.id = 'cambioButton';
                        button.textContent = 'Realizar Cambio';
                        button.onclick = function() {
                            realizarCambio(id_player, match);
                        };
                        button.classList.add('sacarTitulares')
                        formJugador.appendChild(button);
                    }
                } else {
                    over.style.display = 'none';
                }
            }

            function realizarCambio(id_player, match) {
                const checkboxesSeleccionados = document.querySelectorAll('#jugadoresSuplentes .seleccionar-jugador:checked');
                const idsJugadoresSeleccionados = Array.from(checkboxesSeleccionados).map(checkbox => checkbox.value)[0];
                let minutosCambio = document.querySelector('#minutosCAmbio').value

                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "realizarCambio");
                    form_data.append("id_player", id_player);
                    form_data.append("idsJugadoresSeleccionados", idsJugadoresSeleccionados);
                    form_data.append("minutosCambio", minutosCambio);
                    form_data.append("partido", match);

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
                           console.log(data)
                            location.reload();
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });

            }

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
                                if(jugador.equipo === "null" || jugador.equipo === null){
                                    jugador.equipo = "Ninguno"
                                }
                                tablaTodos.row.add([
                                    jugador.nombreCompleto,    // Columna 1: Nombre del jugador
                                    `<input type="checkbox" class="seleccionar-jugador" value="${jugador.id_jugador}">`, // Columna 1: Checkbox
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

            function sumarGol(id_jugador){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "addGol");
                    form_data.append("id_jugador", id_jugador);

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
                            console.log(data)
                            location.reload();
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });
            }

            function obtenerInformacionJugadoresTitulares(partido){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarJugadoresEquipoTitular");
                    form_data.append("partido", partido);

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
                            let jugadores = data.result.response;
                            let match = (jugadores[0].id_partido)
                            tablaTitulares.clear();
                            jugadores.forEach(jugador => {
                                let titularidad;
                                if (jugador.titular === 0) {
                                    tablaSuplentes.row.add([
                                        jugador.nombre,
                                        '<input class="seleccionar-jugador" type="radio" name="suplenteSelect" value="' + jugador.id_jugador + '">'
                                    ]).draw(false);
                                } else {
                                    titularidad = 'Titular';
                                    tablaTitulares.row.add([
                                        jugador.nombre,
                                        `<button class="cambio-${jugador.id_jugador} btnCambio"><i class="fa fa-exchange-alt"></i></button>`,
                                        `<button onclick='sumarGol(${jugador.id_jugador})' class="btnGol"><i class="fa fa-futbol"></i></button>`,
                                    ]);
                                }
                            });
                            tablaTitulares.draw();
                            tablaSuplentes.draw();
                            let overlaySuplentes = document.querySelector('.ventanaModal.suplentes');
                            let buttonsCambio = document.querySelectorAll('.btnCambio'); // Selecciona todos los botones con clase 'btnCambio'

                            buttonsCambio.forEach(button => {
                                button.addEventListener('click', function() {
                                    abrirOverlayCambio(overlaySuplentes, button.classList, match);
                                });
                            });

                            document.querySelector('.ventanaModal.suplentes .overlay').addEventListener('click', function() {
                                abrirOverlay(overlaySuplentes);
                            });

                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });
            }

            function addTitulares(){
                const checkboxesSeleccionados = document.querySelectorAll('.seleccionar-jugador:checked');
                const idsJugadoresSeleccionados = Array.from(checkboxesSeleccionados).map(checkbox => checkbox.value);

                const checkboxesNoSeleccionados = document.querySelectorAll('.seleccionar-jugador:not(:checked)');
                const idsJugadoresNoSeleccionados = Array.from(checkboxesNoSeleccionados).map(checkbox => checkbox.value);

                const currentUrl = new URL(window.location.href); // Crea una instancia de URL desde la URL actual
                const id_partido = currentUrl.searchParams.get('id_partido'); // Obtiene el valor de id_partido

                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "addTitulares");
                    form_data.append("partido", id_partido);
                    form_data.append("titulares", idsJugadoresSeleccionados);
                    form_data.append("suplentes", idsJugadoresNoSeleccionados);

                    for (var value of form_data.values()) {}
                    console.log(form_data)

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
                            console.log(data)
                            location.reload();
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                        });
                });

            }

            function finalizarPartido(){
                const currentUrl = new URL(window.location.href); // Crea una instancia de URL desde la URL actual
                const id_partidoFin = currentUrl.searchParams.get('id_partido'); // Obtiene el valor de id_partido

                var inputValueFinalResultado = document.getElementById('resultadoFinalMatch').value;
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "finalizarMatch");
                    form_data.append("id_partido", id_partidoFin);
                    form_data.append("resultado", inputValueFinalResultado);

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
                            location.reload();
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
        <button class="terminarPartido" >Finalizar</button>
        <button class="inicioPartido">Iniciar Partido</button>
        <div class="marcador">
            <div class="sanAn">
                <p class="nombre">San Andres</p>
                <img src="images/favicon_lienzo.png">
            </div>
            <div class="horario">
                <div class="diaPartido"></div>
                <div class="horaPartido"></div>
            </div>
            <div class="otroEquipo">
                <p class="nombre"></p>
                <img src="">
            </div>
        </div>
        <div class="ventanaModal" style="display: none">
            <div class="overlay"></div>
            <div class="formJugador">
                <div class="todosJugadores">
                    <table id="jugadoresEquipo" class="display" style="width:100%; padding-top: 20px;">
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Titularidad</th>
                        </tr>
                        </thead>
                    </table>
                    <button class="sacarTitulares" onclick="addTitulares()">Añadir Titulares</button>
                </div>
            </div>
        </div>
        <div class="ventanaModal suplentes" style="display: none">
            <div class="overlay"></div>
            <div class="formJugador">
                <div class="todosJugadores">
                    <table id="jugadoresSuplentes" class="display" style="width:100%; padding-top: 20px;">
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Elegir</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <input type="number" min="0" max="70" class="minutosCambio" id="minutosCAmbio">
            </div>
        </div>
        <div class="tablaTitulares">
            <table id="jugadoresTitulares" class="display" style="width:100%; padding-top: 20px;">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cambio</th>
                    <th>Gol</th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="ventanaModal finalizarPartido" style="display: none">
            <div class="overlay"></div>
            <div class="formJugador">
                <p>Resultado</p>
                <input type="text" class="resultadoFinalMatch" id="resultadoFinalMatch" onclick="finalizarPartido()">
                <button onclick="finalizarPartido()">FINALIZAR</button>
            </div>
        </div>
    </div>

@endsection
