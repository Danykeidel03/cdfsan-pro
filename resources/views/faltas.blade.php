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
        var tablaTodos
        $(document).ready(function () {
            document.querySelector('.verFechas').addEventListener('click', abrirOverlay)
            document.querySelector('.overlay').addEventListener('click', abrirOverlay)


            const currentUrl = new URL(window.location.href); // Crea una instancia de URL desde la URL actual
            const id_equipo = currentUrl.searchParams.get('id_equipo'); // Obtiene el valor de id_partido
            obtenerInformacionEquipos(id_equipo);
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
                pageLength: 50,  // Establece la paginación a 50 por página
                initComplete: function () {
                    var $buscar = $('.dt-search input');
                    $buscar.attr('placeholder', 'Buscar en palabras');
                }
            });
            let btnAddFalta = document.querySelector('.crearFaltas');
            btnAddFalta.addEventListener('click', () => addFaltas(id_equipo));
            btnAddFalta.addEventListener('click', () => addNotas(id_equipo));

            // getFechas(id_equipo)

            const inputFecha = document.getElementById("select-fecha-faltas");

            inputFecha.addEventListener("change", (event) => {
                console.log("Fecha seleccionada:", event.target.value);
                getNotasBasedOnDay(event.target.value)
            });


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
                                `<input type="numer" class="actitudJugador" id="${jugador.id_jugador}" />`,
                                `<input type="numer" class="rendimientoJugador" id="${jugador.id_jugador}" />`,
                                `<input type="checkbox" class="faltas-asistencia" id="${jugador.id_jugador}" />`

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

        function addFaltas(equipo){
            Pace.track(function () {

                const checkboxesMarcados = document.querySelectorAll('.faltas-asistencia[type="checkbox"]:checked');
                const arrayCheckboxes = Array.from(checkboxesMarcados).map(checkbox => checkbox.id);

                let fecha = document.querySelector('#fecha-faltas')

                var form_data = new FormData();
                form_data.append("accion", "addFalta");
                form_data.append("equipo", equipo);
                form_data.append("jugadoresFalta", arrayCheckboxes);
                form_data.append("fecha", fecha.value);

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
                        console.log('jugadores sacados');
                    });
            });
        }


        function addNotas(equipo){
            Pace.track(function () {

                const checkboxesNoMarcados = document.querySelectorAll('.faltas-asistencia[type="checkbox"]:not(:checked)');
                const arrayCheckboxes = Array.from(checkboxesNoMarcados).map(checkbox => checkbox.id);
                const arrayNotas = Array.from(document.querySelectorAll('.rendimientoJugador')).map((element, index) => ({
                    id: element.id,
                    rendimiento: element.value,
                    actitud: document.querySelectorAll('.actitudJugador')[index]?.value || null
                }));

                let fecha = document.querySelector('#fecha-faltas')

                var form_data = new FormData();
                form_data.append("accion", "addNotas");
                form_data.append("equipo", equipo);
                form_data.append("jugadoresFalta", arrayCheckboxes);
                form_data.append("fecha", fecha.value);
                form_data.append("nota", JSON.stringify(arrayNotas));

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
                        console.log('jugadores sacados');
                    });
            });
        }

        {{--function getFechas(equipo){--}}

        {{--    var form_data = new FormData();--}}
        {{--    form_data.append("accion", "getFechas");--}}
        {{--    form_data.append("equipo", equipo);--}}

        {{--    fetch("{{ $ajaxUrl }}", {--}}
        {{--        method: 'POST',--}}
        {{--        headers: {--}}
        {{--            "Accept": "application/json",--}}
        {{--            "X-CSRF-TOKEN": "{{ csrf_token() }}",--}}
        {{--        },--}}
        {{--        body: form_data,--}}
        {{--        cache: 'no-cache',--}}
        {{--    })--}}
        {{--        .then(res => {--}}
        {{--            return res.json();--}}
        {{--        })--}}
        {{--        .then(data => {--}}
        {{--            data.forEach( item => {--}}
        {{--                console.log(item.fecha)--}}
        {{--            })--}}
        {{--        })--}}
        {{--        .catch(error => {--}}
        {{--            console.error('GUARDAR error:', error);--}}
        {{--        })--}}
        {{--        .finally(() => {--}}
        {{--            console.log('fechas sacadas');--}}
        {{--        });--}}
        {{--}--}}

        function abrirOverlay(){
            let over = document.querySelector('.ventanaModal');
            if (window.getComputedStyle(over).display === 'none') {
                over.style.display = 'flex';
            } else {
                over.style.display = 'none';
            }
        }

        function getNotasBasedOnDay(day){
            var form_data = new FormData();
            form_data.append("accion", "getNotasBasedOnDay");
            form_data.append("dia", day);

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
                    let tabla = "<table style='width: 100%' border='1'><tr><th>Nombre</th><th>Nota</th></tr>";

                    data.forEach(item => {
                        tabla += `<tr><td>${item.nombre}</td><td>${item.nota}</td></tr>`;
                    });
                    tabla += "</table>";
                    console.log(tabla);
                    document.querySelector(".tablaNotas").innerHTML = tabla;
                })
                .catch(error => {
                    console.error('GUARDAR error:', error);
                })
                .finally(() => {
                    console.log('fechas sacadas');
                });
        }

    </script>
@endpush
@php

    @endphp
@section('content')
    <div id="panel-datos">
        <h1 class="titleJugadores"></h1>
        <button class="verFechas buttonVentana">Ver Notas</button>
        <div class="ventanaModal" style="display: none">
            <div class="overlay"></div>
            <div class="formJugador">
                <p>Seleccciona una fecha</p>
                <input type="date" id="select-fecha-faltas" />
                <div class="tablaNotas"></div>
            </div>
        </div>
        <div class="todosJugadores" style="margin-top: 50px;">
            <input type="date" id="fecha-faltas" />
            <table id="jugadoresEquipo" class="display" style="width:100%; padding-top: 20px;">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Actitud</th>
                    <th>Rendimiento</th>
                    <th>Falta</th>
                </tr>
                </thead>
            </table>
            <button class="crearFaltas buttonVentana" style="margin-top:50px">Añadir Faltas</button>
        </div>
    </div>
@endsection
