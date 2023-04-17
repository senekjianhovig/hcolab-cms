<div class="header-hovig d-flex justify-content-between align-items-center">
    <h4 class="title my-0">{{$title}}</h4>
    <div class="d-flex align-items-center">


    
      @if(env('NOTIFICATION_CENTER_ENABLED') && env('NOTIFICATION_CENTER_ENABLED') == 1)
          <div class="ui icon top left pointing notification-popup-trigger " style="margin-right:50px">  
            <span class="initials">
              <i class="bell icon"></i>
          
            </span>
            <i class="dropdown icon"></i>
          </div>
          
          <div class="ui flowing popup hidden notification-popup-panel dropdown-notifications menu">
           
            <div class="ui loading-screen loading" style="height: 100px;">
            
            </div>
              
          </div>
      @endif


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

