<div class="header-hovig d-flex justify-content-between align-items-center">
    <h4 class="title my-0">{{$title}}</h4>
    <div class="d-flex align-items-center">


        {{-- <div class="dropdown dropdown-notifications">
            <a href="#notifications-panel" class="dropdown-toggle" data-toggle="dropdown">
              <i data-count="0" class="glyphicon glyphicon-bell notification-icon"></i>
            </a>

            <div class="dropdown-container">
              <div class="dropdown-toolbar">
                <div class="dropdown-toolbar-actions">
                  <a href="#">Mark all as read</a>
                </div>
                <h3 class="dropdown-toolbar-title">Notifications (<span class="notif-count">0</span>)</h3>
              </div>
              <ul class="dropdown-menu">
              </ul>
              <div class="dropdown-footer text-center">
                <a href="#">View All</a>
              </div>
            </div>
        </div> --}}

        {{-- <div> --}}
          <div class="ui icon top left pointing notification-popup-trigger " style="margin-right:50px">
            
            <span class="initials">
              <i class="bell icon"></i>
              {{-- <i class="fa-solid fa-bell-on"></i> --}}
              {{-- <i class="fa-solid fa-bell-on"></i> --}}
            </span>
            <i class="dropdown icon"></i>
          </div>
            {{-- <i data-count="0" class="glyphicon glyphicon-bell notification-icon"></i> --}}
           
          <div class="ui flowing popup hidden notification-popup-panel dropdown-notifications menu">
           
            <div class="ui loading-screen loading" style="height: 100px;">
            
            </div>
              
            </div>
        {{-- </div> --}}


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

