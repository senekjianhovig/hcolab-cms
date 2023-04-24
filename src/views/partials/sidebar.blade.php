@php
$current_url = url()->current();
@endphp
<div class="nav main">
    <a href="{{env('APP_URL')}}/cms" class="header">
        @if(env('CMS_LOGO'))
        <img src="{{env('CMS_LOGO')}}" width="180">
        @else
        <img src="{{env('APP_URL')}}/hcolab/cms/assets/svg/logo.svg" width="120">
        @endif
    </a>
    <form method="post" onsubmit="return false" id="nav-search">
        <i class="search icon"></i>
        <input type="search" name="nav-search" placeholder="Search ...">
    </form>
    <div class="body CustomScrollbar">
        <ul>

            <li><a href="/cms"> Dashboard</a></li>
        

            <?php $sidebar = config('pages')['menu']; ?>
           

            @foreach ($sidebar as $item)
           
            @switch($item["type"])
            @case('static')

            @php
                $link_to = str_replace( env('APP_URL') , '' , $item['link_to']);
                $current_to = str_replace(env('APP_URL') , '' , $current_url);
               
            @endphp

            <li> <a @if(array_key_exists('target' , $item)) target="{{$item['target']}}" @endif
                    href="{{$item['link_to']}}" class="@if(str_contains($link_to, $current_to)) active @endif"> {{$item["label"]}} </a> </li>
            @break

            @case('dropdown')
            <li>
                <a href="javascript:;" class="has-submenu">
                    {!!$item["icon"]!!} {{$item["label"]}}
                </a>
                <ul>

                    @foreach($item['children'] as $dropdown_page)

                    @if($dropdown_page['type'] == 'page')

                    @php
                    $entity = $dropdown_page['link_to'];
                    $class_exists = class_exists($entity);


                    @endphp
        
                    @if($class_exists)
                    @php $class = new $entity; @endphp
                    <li>
                        <a href="{{route('page',['page_slug'=> $class->slug ])}}" class="@if(str_contains($current_url, $class->slug)) active @endif"> {!! isset($class->icon) ?? $class->icon !!}
                            {{$class->title}}
                        </a>
                    </li>
                    @endif



                    @else
                    <li> <a @if(array_key_exists('target' , $dropdown_page)) target="{{$dropdown_page['target']}}" @endif
                        href="{{$dropdown_page['link_to']}}"> {{$dropdown_page["label"]}} </a> </li>
                    @endif

                    @endforeach
                </ul>
            </li>
            @break

            @case('page')

            @php
            $entity = $item['link_to'];
            $class_exists = class_exists($entity);

            @endphp

            @if($class_exists)
            @php 
            
            $class = new $entity; 
            
            @endphp
            <li>
                <a href="{{route('page',['page_slug'=> $class->slug ])}}" class="@if(str_contains($current_url, $class->slug)) active @endif"> {!! isset($class->icon) ?? $class->icon !!}
                    {{$class->title}}
                </a>
            </li>
            @endif
            @break

            @case('group label')
            <li class="grouplabel"> {{$item["label"]}} </li>
            @break
            @endswitch

            @endforeach

        </ul>
    </div>
</div>