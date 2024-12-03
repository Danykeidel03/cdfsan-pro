@extends('adminlte::page')

@section('title', 'CDFSAN')

@section('content_header')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
@stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/cabecera.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/equipos.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jugadores.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pdf.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

@stop
@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script>
            $(document).ready(function () {
                let overLay = document.querySelector('.ventanaModal')
                let btnIniciarMatch = document.querySelector('.addPDF')
                overLay.addEventListener('click', () => abrirOverlay(overLay));
                btnIniciarMatch.addEventListener('click', () => abrirOverlay(overLay));
            });

            function abrirOverlay(over){
                if (window.getComputedStyle(over).display === 'none') {
                    over.style.display = 'flex';
                } else {
                    over.style.display = 'none';
                }
            }
        </script>

@endpush
@php

    @endphp
@section('content')
    <div id="panel-datos">
        <button class="addPDF inicioPartido">Añadir PDF</button>
        <div class="ventanaModal" style="display: none">
            <div class="overlay"></div>
            <div class="formJugador">
                <div class="addEjercicio">
                    <button class="addEjercicio-button">Añadir Ejercicio</button>
                </div>
            </div>
        </div>
    </div>

@endsection
