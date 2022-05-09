<div class="nav main">
    <a href="{{route('dashboard')}}" class="header">
        <img src="{{env('APP_URL')}}/hcolab/cms/assets/svg/logo.svg" width="120">
    </a>
    <form method="post" onsubmit="return false" id="nav-search">
        <i class="search icon"></i>
        <input type="search" name="nav-search" placeholder="Search ...">
    </form>
    <div class="body CustomScrollbar">
        <ul>

            <li><a href="/"><i class="fas fa-tachometer-alt mr-3"></i> Dashboard</a></li>

            <?php $sidebar = config('pages')['menu']; ?>
            {{-- @foreach ($pages as $page)

            @switch($page["type"])
            @case('link')
            <li> <a href="{{route('page',['page_slug'=> $page['key'] ])}}"> {{$page["label"]}} </a> </li>
            @break
            @case('dropdown')
            <li>
                <a href="javascript:;" class="has-submenu">
                    {!!$page["icon"]!!} {{$page["label"]}}
                </a>
                <ul>

                    @foreach($page['items'] as $dropdown_page)
                    <li><a href="{{route('page',['page_slug'=> $dropdown_page['key'] ])}}">
                            {{$dropdown_page["label"]}}</a>
                    </li>
                    @endforeach

                </ul>
            </li>
            @break

            @case('group label')
            <li class="grouplabel"> {{$page["label"]}} </li>
            @break
            @endswitch

            @endforeach --}}


            @foreach ($sidebar as $item)

            @switch($item["type"])
            @case('static')
            <li> <a @if(array_key_exists('target' , $item)) target="{{$item['target']}}" @endif
                    href="{{$item['link_to']}}"> {{$item["label"]}} </a> </li>
            @break

            @case('dropdown')
            <li>
                <a href="javascript:;" class="has-submenu">
                    {!!$item["icon"]!!} {{$item["label"]}}
                </a>
                <ul>

                    @foreach($item['children'] as $dropdown_page)

                    @php
                    $link_to = $dropdown_page['link_to'];
                    $namespace = "\App\Pages\\";
                    $entity = $namespace.$link_to;
                    $class_exists = class_exists($entity);
                    @endphp
        
                    @if($class_exists)
                    @php $class = new $entity; @endphp
                    <li>
                        <a href="{{route('page',['page_slug'=> $class->slug ])}}"> {!! isset($class->icon) ?? $class->icon !!}
                            {{$class->title}}
                        </a>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </li>
            @break

            @case('page')

            @php
            $link_to = $item['link_to'];
            $namespace = "\App\Pages\\";
            $entity = $namespace.$link_to;
            $class_exists = class_exists($entity);
            @endphp

            @if($class_exists)
            @php $class = new $entity; @endphp
            <li>
                <a href="{{route('page',['page_slug'=> $class->slug ])}}"> {!! isset($class->icon) ?? $class->icon !!}
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