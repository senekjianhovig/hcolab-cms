@if($seo)

@if($seo->title) <title> {{$seo->title}}</title> @endif
@if($seo->description) <meta name="description" content="{{$seo->description}}"> @endif
@if($seo->keywords) <meta name="keywords"content="{{$seo->keywords}}"> @endif

@elseif($default_seo)

@if($default_seo->title) <title> {{$default_seo->title}}</title> @endif
@if($default_seo->description) <meta name="description" content="{{$default_seo->description}}"> @endif
@if($default_seo->keywords) <meta name="keywords"content="{{$default_seo->keywords}}"> @endif

@else

<title> {{env('APP_NAME')}} </title>

@endif