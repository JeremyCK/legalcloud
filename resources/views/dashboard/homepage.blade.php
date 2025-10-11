@extends('dashboard.base')

@section('css')
<link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container-fluid">
  <div class="fade-in">

  </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script src="{{ asset('js/coreui-chartjs.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>
@endsection