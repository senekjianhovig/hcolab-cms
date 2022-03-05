<tr id="{{$identifier}}">
    <td style="font-size: 12px;"> {{$variant}} <input type="hidden" name="variant[]" value="{{$identifier}}"> </td>
    
    @isset($hide)
    <td> <div class="ui slider checkbox">
        <input type="checkbox" name="hide_product[]" value="{{$identifier}}" @if($hide) checked @endif>
      </div> 
    </td>
    @endisset

    @if(!is_null($images))
    <td>  
        <div class="ui fluid selection dropdown">
            <input type="hidden" name="image[]" value="{{$image}}">
            <i class="dropdown icon"></i>
            <div class="default text">Image</div>
            <div class="menu">
                @foreach($images as $preview)
              <div class="item"  data-value="{{$preview->id}}">
                  @php
                      $url = (bool) $preview->external ? $preview->url : env('APP_URL').'/storage/'.$preview->url;
                  @endphp
                <img class="ui image" src="{{$url}}">
                    
              </div>
              @endforeach
            </div>
          </div>
    </td>
    @endif
    
    @isset($sku)
    <td> <input type="text" name="sku[]" value="{{$sku}}"> </td>
    @endisset
    @isset($barcode)
    <td> <input type="text" name="barcode[]" value="{{$barcode}}"> </td>
    @endisset
  
    <td> <input type="text" name="stock_quantity[]" value="{{$stock_quantity}}"> </td>
    <td> <input type="text" name="price[]" value="{{$price}}"> </td>
    <td> <input type="text" name="discount[]" value="{{$discount}}"> </td>
    <td> <input type="text" name="cost[]" value="{{$cost}}"> </td>
    @if(!isset($deletable) || (isset($deletable) && $deletable))
     <td>
        <div class="ui icon top left pointing dropdown button">
            <i class="ellipsis  vertical icon"></i>
            <div class="menu">
                <a href="{{route('page.edit', ['page_slug' => 'products' , 'id' => $id, 'redirect_url'=> url()->current() ])}}" onclick="" class="item">Edit</a>
            </div>
        </div>
     </td>
     @endif
 </tr>