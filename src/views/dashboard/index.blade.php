@extends('CMSViews::layout.layout', ['title' => 'Dashboard'])

@section('head')
<title> Dashboard </title>

<style>

h3 {
    margin-top: 30px !important;
}

    </style>

@endsection

@php
    $charts = [];
@endphp

@section('content')

<div class="container-fluid mt-4 mb-5">
    {{-- <h3>Welcome to our Content Management System!</h3> --}}
    {{-- <div class="ui indicating progress active" data-percent="95">
        <div class="bar" style="transition-duration: 300ms; width: 27%;"></div>
        <div class="label">Completed 27%</div>
    </div> --}}
{{-- <div class="ui segment raised"> --}}

    <div class="row">

        @foreach (config('pages.graphs') as $settings)

            @php
                $chart = new \hcolab\cms\repositories\Chart($settings);
            @endphp

            @if($settings['type'] == 'table')
            <div class="@isset($settings['column_class']){{$settings['column_class']}} @else col-lg-12 @endisset">
                <div class="ui segment raised">
                <h3>{!! $chart->options['chart_title'] !!}</h3>

                @php
                    $data = $chart->getDatasets();
                   
                @endphp
                {{-- @dd($data) --}}
            <div style="width: 100%; overflow: auto">
            <table class="ui table">
                <thead>
                  <tr>
                      @foreach($settings['columns'] as $column)
                        <th>{{$column['label'] }}</th>
                      @endforeach
                  </tr>
                </thead>
                <tbody>
                    @foreach($data[0] as $row)
                        <tr>
                            @foreach($settings['columns'] as $column)
                                <td>{{$row->{$column['key']} }}</td>
                            @endforeach
                        </tr>
                  @endforeach
                  
                </tbody>
              </table>
            </div>
            </div>
            </div>
            @continue
            @endif


            @php
        
                $charts [] = $chart;
               
            @endphp

            <div class="@isset($settings['column_class']){{$settings['column_class']}} @else col-lg-12 @endisset">
                <div class="ui segment raised">
                <h3>{!! $chart->options['chart_title'] !!}</h3>
                {!! $chart->renderHtml() !!}
                </div>
            </div>

        @endforeach
        
    </div>
</div>
{{-- </div>  --}}
@endsection

@section('scripts')

{!! $chart->renderChartJsLibrary() !!}

@foreach($charts as $chartJs)

{!! $chartJs->renderJs() !!}

@endforeach


@endsection