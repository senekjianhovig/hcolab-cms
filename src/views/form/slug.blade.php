@php
$name = $element->name;
if(!property_exists($data,$name)){ $data->$name = ''; }

$label_exists = !isset($element->ui->disable_label) || isset($element->ui->disable_label) && $element->ui->disable_label == false;
$margin_exists = !isset($element->ui->disable_margin) || isset($element->ui->disable_margin) && $element->ui->disable_margin == false;

@endphp

<div class="{{$element->ui->container}} @if($margin_exists) mb-4 @endif">
    <div class="field">
      @if($label_exists)  <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label> @endif
        <input readonly type="text" name="{{$name}}" @if((int) $element->ui->required==1) required @endif
        placeholder="Enter {{strtolower($element->ui->label)}}"
        value="@if($data){{ $data->$name }}@endif" >
    </div>
</div>


<script>
    document.getElementsByName('{{$element->ui->slugable_by}}')[0].addEventListener("input", function(event) {
        var slug = slugify(this.value);
        document.getElementsByName('{{$element->name}}')[0].value = slug; 
        var url = window.location.href;
        var result = validateSlug(url+'/validate/slug/'+ '{{$element->name}}' +'/'+slug , '{{$element->name}}');
        
    });
</script>

<style>
.invalid-field input{
    border-color: red !important
}
</style>
