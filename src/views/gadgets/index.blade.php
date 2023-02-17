
@foreach($gadgets as $gadget)
<div class="{{$gadget->container_class}}">
    @include('CMSViews::gadgets.types.'.$gadget->type , [ 'gadget' => $gadget , 'data' => $gadget->data])
</div>
@endforeach

