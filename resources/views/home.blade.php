@extends('adminlte::page')

@section('title', 'CDFSAN')

@section('content_header')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
@stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/cabecera.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

@stop
@push('js')

        <script>
            $(document).ready(function () {

            });

        </script>



@endpush
@php

    @endphp
@section('content')
    <div id="panel-datos">


    </div>

@endsection
