@php

$notifications = App\Models\CmsNotification::where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })->orderBy('id', 'desc')->get()->take(5);

@endphp

@if(count($notifications) > 0)

@foreach($notifications as $notification)
<a class="item d-flex"  href="{{route('page.show' , ['page_slug' => $notification->page_slug , 'id'=> $notification->row_id])}}"> 
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
<a style="width:100%;height: 45px;text-align: center;display: flex;align-items: center;justify-content: center;background-color: #f9f9f9;border-top: 1px solid #eee;color: #000" href="{{route('notification-center')}}"> View All</a>

@else
<div style="text-align:center;font-weight: bold; height:100px; display:flex;    align-items: center;
justify-content: center;"  > No notifications yet </div>
@endif

<style>
.bullet{
    display: inline-block;
    min-height: 8px;
    min-width: 8px;
    background-color: #418f8f;
    border-radius: 50%;
    margin-left: 5px;
}
  </style>