@extends('CMSViews::layout.layout', ['title' => 'Generate Entities'])

@section('head')
<title> Generate Entities </title>
@endsection

@php
$files = File::files(app_path()."/Pages/");
$entities = DB::table('entity_versions')->where('deleted', 0)->get()->keyBy('entity')->toArray();

@endphp

@section('content')
<div class="container-fluid my-3">

    <div class="ui segments">

        @foreach($files as $file)
        @php
        $file_name = $file->getFilename();
        $newFileName = str_replace('.php' , '' , $file_name);
        $settings = get_page_settings($newFileName);


        $namespace = '\\App\\Pages\\' . $newFileName;
        $class = new $namespace;

        $latest = $entities[$newFileName]->version == $class->version;

        @endphp
        @if(!$latest)
        <div class="ui segment  d-flex align-items-center justify-content-between">
            <div>
                <div><strong> {{$settings['title']}} </strong> </div>
                <i style="font-size:14px ;color: #db2828"> new version
                    {{$class->version}} </i>
            </div>

            <a class="ui button" type="submit"> Generate</a>
        </div>
        @endif
        @endforeach

    </div>

</div>
@endsection

@section('scripts')

@endsection