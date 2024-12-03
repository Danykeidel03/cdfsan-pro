@extends('adminlte::page')

@section('title', 'CDFSAN')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/cabecera.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/equipos.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

@stop
@push('js')

        <script>
            $(document).ready(function () {
                obtenerInformacionEquipos();
            });

            /**
             * Obtenemos la informacion para mostrar en el grid de idiomas
             */
            function obtenerInformacionEquipos(equipo){
                Pace.track(function () {

                    var form_data = new FormData();
                    form_data.append("accion", "mostarEquipos");
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
                            equipos = (data.result.response)
                            let divEquipos = document.querySelector('.todosEquipos')
                            equipos.forEach(equipo => {
                                let divEquipo = document.createElement('div');
                                divEquipo.classList.add('equipo-container');
                                divEquipo.innerHTML = `
                                    <div class="equipo">
                                        <img class="imgEquipo" src="images/favicon_lienzo.png" alt="Imagen de ${equipo.categoria}">
                                        <div class="infoEquipo">
                                            <a class="tituloEquipo">${equipo.categoria}</a>
                                            <p class="entrenadores">${equipo.entrenadores}</p>
                                        </div>
                                    </div>
                                `;
                                divEquipo.querySelector('.equipo').addEventListener('click', function() {
                                    let equipoId = equipo.id_equipo;
                                    let currentUrl = new URL(window.location.origin + window.location.pathname.split('/').slice(0, -2).join('/') + '/public/jugadores');
                                    if (equipoId !== 'all') {
                                        currentUrl.searchParams.set('id_equipo', equipoId);
                                    } else {
                                        currentUrl.searchParams.delete('id_equipo');
                                    }
                                    window.location.href = currentUrl.toString();
                                });
                                divEquipos.appendChild(divEquipo);
                            })
                        })
                        .catch(error => {
                            console.error('GUARDAR error:', error);
                        })
                        .finally(() => {
                            console.log('equipos sacados');
                        });
                });
            }
        </script>



@endpush
@php

    @endphp
@section('content')
    <div id="panel-datos">

        <h1 class="tittleEquipos">Listado de equipos</h1>
        <div class="todosEquipos" style="margin-top: 50px;"></div>
    </div>

@endsection
