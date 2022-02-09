<div class="nav main">
    <a href="{{route('dashboard')}}" class="header">
        <img src="/assets/svg/logo.svg" width="120">
    </a>
    <form method="post" onsubmit="return false" id="nav-search">
        <i class="search icon"></i>
        <input type="search" name="nav-search" placeholder="Search ...">
    </form>
    <div class="body CustomScrollbar">
        <ul>

            <li><a href="/"><i class="fas fa-tachometer-alt mr-3"></i> Dashboard</a></li>

            <?php $pages = config('pages')['menu']; ?>
            @foreach ($pages as $page)

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

            @endforeach



        </ul>
    </div>
</div>