@php
$name = $element->name;
$target_page = new $element->ui->target_page;
$variant_page = new $element->ui->variant_page;
$variants = \Illuminate\Support\Facades\DB::table($variant_page->entity)->where('deleted',0)->get()->groupBy('type');
$target_page->setElements();
$taxable = false;

$edit_mode = false;$is_variants = true;

if($data && isset($data->id) && !empty($data->id)){
    
    $edit_mode = true;
    $existing_products = $target_page->getProductsByGroupID($data->id, $element);
    $is_variants = is_array($existing_products) && !array_key_exists('no-variant' , $existing_products);
    $ids =  json_decode($data->gallery);
    $previews = \hcolab\cms\models\File::whereIn('id', $ids)->where('deleted',0)->get();

    try {
        // dd($existing_products);
        $clone_products = collect($existing_products)->values();
       
        $taxable = $clone_products[0]->charge_tax == 1;
    } catch (\Throwable $th) {
        $taxable = false;
    }
   
}
@endphp



<div class="variants-panel mb-4" >

    <div class="mb-4">
      
    <div class="ui header small mb-4">Charge TAX?</div>
    <div class="ui checkbox ">
        <input type="checkbox" @if($taxable) checked @endif name="charge_tax">
        <label>This product is taxable</label>
      </div>
        
    </div>

    @if($edit_mode)
    <div id="existing-variants" class="existing-variants mb-4">
        <div class="mb-4">
            <div class="ui header small mb-0">Exising Variants</div>
        </div>
       
        <table class="ui stackable existing-area  table mb-4">
           @include('CMSViews::ecom.components.variants.table-header')
            <tbody >
                @foreach($existing_products as $key => $product)
                    @include('CMSViews::ecom.components.variants.table-row',[
                        'id' => $product->id,
                        'variant' => $product->identifier_label,
                        'identifier' => $key,
                        'image' => $product->image,
                        'images' => $previews, 
                        'sku' => $product->sku,
                        'barcode' =>$product->barcode,
                        'hide' => $product->hide == 1,
                        'stock_quantity' => $product->stock_quantity,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'cost' => $product->cost,
                    ])
                @endforeach
            </tbody>
            </table>
  
        </div>
    @endif

    
    @if(!$edit_mode)
    <div class="ui header small mb-4">Variants</div>
    @endif
    
   
    
    @if($is_variants)
    <div class="ui checkbox mb-4">
        <input type="checkbox" name="include_variant" onchange="ToggleVariants($(this))">
        <label>Add variants</label>
    </div>

    <input type="hidden" name="include_existing_variant" value="on">

    @else
    
    <input type="hidden" name="include_variant" value="@if(isset($existing_products) && count($existing_products) > 0){{"on"}}@else{{"off"}}@endif">
    @endif
    
    
    <div class="variation-area">
  
    <div  class="row">
        @foreach($variants AS $key => $options)
            <div class="col-lg-6">
            <select id="variant_{{$key}}" multiple class="ui fluid search dropdown" onchange="variantsHandler($(this))">
                <option value="" disabled selected>Choose {{$key}}</option>
                @foreach($options AS $option)
                    <option value="{{$option->id}}/{{$option->label}}">{{ $option->label }}</option>
                @endforeach
            </select>
            </div>
        @endforeach
    </div>
        <table class="ui stackable table">
            @include('CMSViews::ecom.components.variants.table-header' , ['type' => 'new'])
            <tbody id="variants-body">
                   <tr> 
                       <td colspan="6"> Please select variants </td>
                   </tr>
            </tbody>
        </table>
    </div>


    @if(!$edit_mode)
    <div class="simple-variants mb-4">
            <table class="ui stackable  table">
                <thead>
                    @include('CMSViews::ecom.components.variants.table-header' , ['type' => 'new' , 'deletable' => false])
                </thead>
                <tbody>
                    @include('CMSViews::ecom.components.variants.table-row',[
                        'variant' => 'No Variants',
                        'identifier' => 'no-variant',
                        'stock_quantity' => '',
                        'price' => '',
                        'discount' => '',
                        'cost' => '',
                        'deletable' => false,
                        'image' => '',
                        'images' => null
                    ])
                </tbody>
            </table>
    </div>
@endif

</div>

<script>

function ToggleVariants(elem){
    if($(elem).parent().hasClass('checked') == true){
        $(elem).parents('.variants-panel').find('.simple-variants').slideUp(0);
        $(elem).parents('.variants-panel').find('.variation-area').slideDown();
        
    }else{
        $(elem).parents('.variants-panel').find('.variation-area').slideUp(0);
        $(elem).parents('.variants-panel').find('.simple-variants').slideDown();
    }
}

function variantsHandler(elem){
    var variants = {};
    var variant_types = {};   
    var inputs = elem.parents('.variants-panel').find('select');
     

    var object = new Array();
    var args = new Array();
    var size = 0;
    
    inputs.each((index,element) => {
        var values = $(element).val();
        var mixedValues = new Array();

        values.forEach(value => {
            var split = value.split('/');
            variants["a"+split[0]] = split[1];
            variant_types["a"+split[0]] = $(element).prop('id').replace('variant_' , '');
            mixedValues.push(split[0]);
        });

        
        if(mixedValues.length > 0){
            args.push(mixedValues);
        }
        

         object.push({"name" : $(element).prop('id') , "values" : mixedValues });

     });


     var result ="";

     const cartesian = (...a) => a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
     console.log(args.length);

    if(args.length > 0){
        output = cartesian(...args)

       

        output.forEach(e => {
            var label = "";
            var id_val = "";
            
            for(var i=0 ; i < args.length ; i++){ 
                let seperator = i == 0 ? '' : ' / ';
                let seperator2 = i == 0 ? '' : '-';
                label+= `${seperator} ${variants["a"+e[i]]}`; 
                id_val+= `${seperator2}${variant_types["a"+e[i]]}H${e[i]}`; 
            }

            
            if($(".existing-area #"+id_val).length == 0 ){
                
            result += `
                    <tr id="${id_val}">
                        <td> ${label} <input type="hidden" name="variant[]" value="${id_val}" </td>
                        <td> <input type="text" name="stock_quantity[]"> </td>
                        <td> <input type="text" name="price[]" > </td>
                        <td> <input type="text" name="discount[]"> </td>
                        <td> <input type="text" name="cost[]"> </td>
                        <td> <button class="ui icon button" onclick="$(this).parents('tr').remove()" type"button"> <i class="trash icon"></i> </button> </td>
                    </tr>
                `;
            }
        });

        $('#variants-body').html(result);
     
    }else{
        $('#variants-body').html(`<tr> <td colspan="6"> Please select variants </td></tr>`);
    }

    semanticInit();
}



</script>