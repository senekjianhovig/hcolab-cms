@php
$name = Str::slug($element->db_name,'_');
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }

$items = [];
try {
$items = json_decode($data->$name);
} catch (\Throwable $th) {
$items = [];
}


@endphp



<div class="col-lg-12">
    <input type="hidden" name="{{$name}}" value="@if($data){{ $data->$name }}@endif">
    <label style="display: block;
    margin: 0 0 .28571429rem 0;
    color: rgba(0,0,0,.87);
    font-size: .92857143em;
    font-weight: 700;
    text-transform: none;">{{$element->label}} @if((int) $element->required==1) * @endif</label>
    <div class="row">
        @foreach($items as $item)

        <div class="col-lg-12 mb-3">
            <div class="ui fluid action input">
                <input type="text" value="{{$item->link}}">
                <button type="button" class="ui right labeled icon button">
                    <i class="copy icon"></i>
                    Copy
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>