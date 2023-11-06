@extends('CMSViews::layout.layout', ['title' => "Notification Center"])
@section('head')
<title>Notification Center</title>

@php
use Carbon\Carbon;
@endphp

<style>



.notification-item{
    text-decoration: none;
    color : #000;
    background-color: white;
    border-top: 1px solid #eee;
    padding: 20px;
    width: 100%;
    /* width:600px;  */
     /* max-width: 100%;  */
}

.notification-item:hover{
    color: #000;
    background-color: #f6f6f6
    /* box-shadow: 0 0 3px 0 rgba(0,0,0,0.1); */
}
.accordion{
    width: 100% !important
}

.accordion .content {
    padding: 0 !important;
}

.accordion .title{
    color : #000 !important;
}
.bullet{
    display: inline-block;
    min-height: 8px;
    min-width: 8px;
    background-color: #418f8f;
    border-radius: 50%;
    margin-left: 5px;
}
    </style>
@endsection


@php
$notifications_by_slugs = App\Models\CmsNotification::where('deleted',0)->orderBy('id', 'desc')->where('created_at' , '>' , Carbon::now()->subMonth())->get()->groupBy('page_slug');

@endphp


@section('content')

<div class="container-fluid my-3">


    <div class="ui styled accordion">
       
  

   
    @foreach($notifications_by_slugs as $page_slug => $notifications)
    


    @php
        $page = (new \hcolab\cms\controllers\PageController)->initializeRequest($page_slug);
        if (is_null($page)) {
           continue;
        }

    @endphp


<div class="title @if($loop->first) active @endif">
    <i class="dropdown icon"></i>
    {{$page->title}}
  </div>
  <div class="content @if($loop->first) active @endif">
    <div class="transition @if($loop->first) visible @else hidden @endif">

    @foreach($notifications as $notification)
    <a class="notification-item item d-flex"  href="{{route('page.show' , ['page_slug' => $notification->page_slug , 'id'=> $notification->row_id])}}"> 
        <div style="width: 50px;font-size: 25px; align-self:center" > <i class="bell icon" style="color: #cce2ff"></i> </div>
        <div style="flex-1">
        <div class="mb-3">
          <div class="mb-2" style="font-size:16px"> <b> {{$notification->title}}</b> @if($notification->read == 0) <span class="bullet"> </span> @endif </div>
          <div style="font-size: 14px"> {{$notification->description}} </div>
        </div>
      <div class="item-date" style="color: grey; font-size: 12px;"> {{$notification->created_at->diffForHumans()}} </div>
        </div>
      </a>
      @endforeach

    </div>
</div>

    @endforeach
   
</div>

</div>
@endsection

@section('scripts')
<script>


</script>
@endsection