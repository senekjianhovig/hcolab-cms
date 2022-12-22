<div class="header-hovig d-flex justify-content-between align-items-center">
    <h4 class="title my-0">{{$title}}</h4>
    <div class="d-flex align-items-center">
        <div class="welcome">Welcome <br>  {{request()->admin->first_name}} </div>

        <div class="ui icon top left pointing dropdown ">
            <span class="initials">{{get_name_initials([request()->admin->first_name , request()->admin->last_name])}}</span>
            <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item" href="{{route('change-password')}}"> Change Password </a>
                <a class="item" href="{{route('logout')}}"> Logout </a>
            </div>
        </div>
    </div>
</div>