@php
$name = $element->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = []; }

// Repeater data is now stored as array (not JSON string) - handle both formats for backward compatibility
$items = $data->$name;

// Convert stdClass to array if needed
if(is_object($items)){
    $items = json_decode(json_encode($items), true);
}

if(is_string($items)){
    // Backward compatibility: if it's a JSON string, decode it
    $items = json_decode($items, true);
}

// Ensure $items is always an array
if(!is_array($items)){ $items = []; }

// Convert any stdClass objects inside the array to arrays
$items = array_map(function($item){
    if(is_object($item)){
        return json_decode(json_encode($item), true);
    }
    return $item;
}, $items);

$fieldsRaw = $element->ui->fields ?? [];

// Normalize to array of objects: [['name' => 'x', 'type' => 'text'], ['name' => 'y', 'type' => 'select', 'options' => [...]]]
// Supports both legacy key-value array and new array-of-objects with optional 'options' for select
$fields = [];
$isAssoc = !isset($fieldsRaw[0]) || !is_array($fieldsRaw[0]) || !array_key_exists('name', $fieldsRaw[0]);
if ($isAssoc) {
    foreach ($fieldsRaw as $fieldName => $fieldType) {
        $fields[] = ['name' => $fieldName, 'type' => $fieldType, 'label' => null, 'options' => []];
    }
} else {
    foreach ($fieldsRaw as $f) {
        $name = $f['name'] ?? $f['key'] ?? '';
        $type = $f['type'] ?? 'text';
        $label = $f['label'] ?? null;
        $options = $f['options'] ?? [];
        if (isset($related_tables) && !empty($related_tables) && isset($related_tables[$name]['data'])) {
            $options = $related_tables[$name]['data'];
        }
        $fields[] = ['name' => $name, 'type' => $type, 'label' => $label, 'options' => $options];
    }
}
// For legacy normalized entries (from $isAssoc), fill options from related_tables for select
foreach ($fields as &$f) {
    if (($f['type'] ?? '') === 'select' && empty($f['options']) && isset($related_tables[$f['name']]['data'])) {
        $f['options'] = $related_tables[$f['name']]['data'];
    }
    // Normalize options to [['id' => x, 'label' => y]] for JSON/JS (handle objects and arrays)
    if (!empty($f['options'])) {
        $opts = [];
        foreach ($f['options'] as $o) {
            if (is_object($o)) {
                $opts[] = ['id' => $o->id ?? $o->name ?? null, 'label' => $o->label ?? $o->title_en ?? $o->name ?? $o->id ?? ''];
            } else {
                $opts[] = is_array($o) ? ['id' => $o['id'] ?? $o['value'] ?? null, 'label' => $o['label'] ?? $o['text'] ?? $o['id'] ?? ''] : ['id' => $o, 'label' => (string)$o];
            }
        }
        $f['options'] = $opts;
    }
}
unset($f);
@endphp

<div class="{{ $element->ui->container }} mb-4 repeater-field-container" data-repeater-name="{{$name}}">
    <div class="c-label">
        {{$element->ui->label}} @if($element->ui->required) * @endif
    </div>

    <div class="repeater-wrapper" data-field-name="{{$name}}" data-fields="{{json_encode($fields)}}">
        <div class="repeater-items">
            @foreach($items as $index => $item)
                @php
                    // Convert item to array if it's an object
                    if(is_object($item)){
                        $item = json_decode(json_encode($item), true);
                    }
                    if(!is_array($item)){ $item = []; }
                @endphp
                <div class="repeater-item" data-index="{{$index}}">
                    <div class="repeater-item-header">
                        <span class="repeater-item-title">Item #{{$index + 1}}</span>
                        <button type="button" class="repeater-remove-btn" onclick="if(typeof window.removeRepeaterItem === 'function') { window.removeRepeaterItem(this); }">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                    <div class="repeater-item-content">
                        @foreach($fields as $field)
                            @php
                                $fieldName = $field['name'];
                                $fieldType = $field['type'];
                                $fieldValue = $item[$fieldName] ?? '';
                                $fieldLabel = $field['label'] ?? ucfirst(str_replace('_', ' ', preg_replace('/_id$/', '', $fieldName)));
                            @endphp
                            
                            @if($fieldType == 'file' || $fieldType == 'image')
                                <div class="repeater-field col-lg-12 mb-3">
                                    <label>{{$fieldLabel}}</label>
                                    @php
                                        // Use bracket-free name for dropzone to avoid CSS selector issues
                                        $dropzoneName = "upld_{$name}_{$index}_{$fieldName}";
                                        $actualFieldName = "{$name}[{$index}][{$fieldName}]"; // Actual form field name
                                        $previewWrapperId = "preview-{$name}-{$index}-{$fieldName}";
                                    @endphp
                                    <div class="dropzone-upload-wrapper" 
                                         name="{{$dropzoneName}}" 
                                         data-actual-field-name="{{$actualFieldName}}"
                                         data-preview-id="{{$previewWrapperId}}" 
                                         is_multiple="false">
                                        <div class="upload-area" onclick="$(this).parent().click()">
                                            Choose a file
                                        </div>
                                        <div class="">
                                            <div class="dropzone-preview-wrapper dropzone-preview-wrapper-{{$dropzoneName}}" id="{{$previewWrapperId}}"></div>
                                            <div class="preview-area">
                                                @if(!empty($fieldValue))
                                                    @php
                                                        $previewFile = \hcolab\cms\models\File::where('name', $fieldValue)->where('deleted',0)->first();
                                                    @endphp
                                                    @if($previewFile)
                                                        @include('CMSViews::form.file-preview', [
                                                            'value'=> $previewFile->name,
                                                            'name' => "tmp_{$actualFieldName}", 
                                                            'mime_category' => $previewFile->mime_category, 
                                                            'url' => (bool) $previewFile->external ? $previewFile->url : env('DATA_URL').'/'.$previewFile->url, 
                                                            'display_name' => $previewFile->original_name,
                                                            'preview' => true
                                                        ])
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="{{$actualFieldName}}" value="{{$fieldValue}}">
                                </div>
                            @elseif($fieldType == 'textarea')
                                <div class="repeater-field col-lg-12 mb-3">
                                    <label>{{$fieldLabel}}</label>
                                    <textarea name="{{$name}}[{{$index}}][{{$fieldName}}]" class="form-control" rows="3">{{$fieldValue}}</textarea>
                                </div>
                            @elseif($fieldType == 'select')
                                @php
                                    $options = $field['options'] ?? [];
                                @endphp
                                <div class="repeater-field col-lg-12 mb-3">
                                    <label>{{$fieldLabel}}</label>
                                    <select name="{{$name}}[{{$index}}][{{$fieldName}}]" class="ui fluid search dropdown form-control">
                                        <option value="" disabled selected>Choose {{$fieldLabel}}</option>
                                        @foreach($options as $option)
                                            @php
                                                $optId = is_array($option) ? ($option['id'] ?? $option['value'] ?? '') : ($option->id ?? $option->name ?? '');
                                                $optLabel = is_array($option) ? ($option['label'] ?? $option['text'] ?? $optId) : ($option->label ?? $option->title_en ?? $option->name ?? $option->id ?? '');
                                            @endphp
                                            <option value="{{ $optId }}" @if($fieldValue == $optId) selected @endif>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="repeater-field col-lg-12 mb-3">
                                    <label>{{$fieldLabel}}</label>
                                    <input type="text" name="{{$name}}[{{$index}}][{{$fieldName}}]" class="form-control" value="{{$fieldValue}}">
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
        <button type="button" class="repeater-add-btn" onclick="if(typeof window.addRepeaterItem === 'function') { window.addRepeaterItem(this); } else { alert('Repeater function not loaded. Please refresh the page.'); }">
            <i class="fas fa-plus"></i> Add Item
        </button>
    </div>
</div>

<style>
.dropzone-upload-wrapper{
    background: transparent;
    border: 1px solid rgba(34, 36, 38, 0.15);
    color: rgba(0, 0, 0, 0.87);
    border-radius: 0.28571429rem;
    box-shadow: 0 0 0 0 transparent inset;
    transition: color 0.1s ease, border-color 0.1s ease;
    position: relative;
    cursor: pointer;
}

.dropzone-upload-wrapper .upload-area{
   min-height: 100px;
    text-align: center;
    align-items: center;
    justify-content: center;
    display: flex;
    color: #c1c3c5;
    position: relative;
    z-index: 0;
    cursor: pointer;
}

.dropzone-preview-wrapper{
    
}

.dz-preview{
    padding: 10px;
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #eee;
    background-color: #f8f7f7;
    border-radius: 0.28571429rem;
    gap:10px;
    box-shadow: 0 0 0 0 transparent inset;
}

.video-thumb-remove::after{
    display: none !important;
}

.dz-details{
    /* width: 200px; */
}

.dz-image{
    /* background-color: #eee; */
}
.dz-image img{
    width: 60px;
    object-fit: contain;
}

.dz-preview-info{
    display: flex;
    flex: 1 !important;
    gap:10px;
}

.dz-progress {
      position: relative;
      width: 100%;
      height: 3px;
     
      background-color: #f2f2f2;
      
    }

    .dz-progress .dz-upload {
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background-color: blue;
      transition: all 0.35s ease-in-out;
    }

    .dz-details{
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap:12px;
        flex:1
    }

    .dz-success-mark { color: #4CAF50 }

    .dz-success-mark{ display: none; font-size: 12px; }

    .dz-complete .dz-success-mark{ display: block !important }

    .dz-complete .dz-upload{
        background: #4CAF50 !important;
    }

    .filename-wrapper{
        font-size: 14px;
        
        display: flex;
        gap:10px;
    }

    .dz-filename{
        font-weight: bold
    }

    a.dz-remove{
        display: none;
    }
    .dz-remove{
        background: transparent;
    border: none;
    outline: 0;
    cursor: pointer;
    color: indianred;
    }

.repeater-wrapper {
    border: 1px solid rgba(34, 36, 38, 0.15);
    border-radius: 0.28571429rem;
    padding: 15px;
    background: #fafafa;
}

.repeater-item {
    background: white;
    border: 1px solid #ddd;
    border-radius: 0.28571429rem;
    margin-bottom: 15px;
    padding: 15px;
}

.repeater-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.repeater-item-title {
    font-weight: bold;
    color: #333;
}

.repeater-remove-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.repeater-remove-btn:hover {
    background: #c82333;
}

.repeater-add-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    margin-top: 10px;
}

.repeater-add-btn:hover {
    background: #218838;
}

.repeater-item-content {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.repeater-field {
    margin-bottom: 0;
}

.repeater-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.repeater-field .form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>

<script>
// Run after jQuery is loaded (repeater may be rendered before layout scripts)
(function initRepeaterWhenReady() {
    if (typeof window.jQuery !== 'undefined') {
        window.jQuery(document).ready(function() {
            window.jQuery('.repeater-wrapper select.ui.dropdown').dropdown();
        });
    } else {
        setTimeout(initRepeaterWhenReady, 50);
    }
})();

// Make functions globally available - use jQuery (not $) so we work regardless of load order
window.addRepeaterItem = function(btn) {
    if (typeof window.jQuery === 'undefined') {
        console.error('jQuery is not loaded. Cannot add repeater item.');
        return false;
    }
    
    var wrapper = $(btn).closest('.repeater-wrapper');
    if (!wrapper.length) {
        console.error('Repeater wrapper not found');
        return false;
    }
    
    var fieldName = wrapper.data('field-name');
    var fields = wrapper.data('fields');
    
    if (!fieldName || !fields) {
        console.error('Repeater field data not found. Field name:', fieldName, 'Fields:', fields);
        return false;
    }
    
    var itemsContainer = wrapper.find('.repeater-items');
    var currentIndex = itemsContainer.find('.repeater-item').length;
    
    var itemHtml = '<div class="repeater-item" data-index="' + currentIndex + '">' +
        '<div class="repeater-item-header">' +
        '<span class="repeater-item-title">Item #' + (currentIndex + 1) + '</span>' +
        '<button type="button" class="repeater-remove-btn" onclick="if(typeof window.removeRepeaterItem === \'function\') { window.removeRepeaterItem(this); }">' +
        '<i class="fas fa-times"></i> Remove' +
        '</button>' +
        '</div>' +
        '<div class="repeater-item-content">';
    
    var fieldsList = Array.isArray(fields) ? fields : Object.keys(fields).map(function(k) { return { name: k, type: fields[k], label: null, options: [] }; });
    for (var i = 0; i < fieldsList.length; i++) {
        var field = fieldsList[i];
        var fieldNameKey = field.name || field.key;
        var fieldType = field.type || 'text';
        var fieldLabel = field.label || fieldNameKey.replace(/_id$/, '').replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        var fieldOptions = field.options || [];
        
        if (fieldType == 'file' || fieldType == 'image') {
            var dropzoneName = 'upld_' + fieldName + '_' + currentIndex + '_' + fieldNameKey;
            var actualFieldName = fieldName + '[' + currentIndex + '][' + fieldNameKey + ']';
            var previewWrapperId = 'preview-' + fieldName + '-' + currentIndex + '-' + fieldNameKey;
            itemHtml += '<div class="repeater-field col-lg-12 mb-3">' +
                '<label>' + fieldLabel + '</label>' +
                '<div class="dropzone-upload-wrapper" name="' + dropzoneName + '" data-actual-field-name="' + actualFieldName + '" data-preview-id="' + previewWrapperId + '" is_multiple="false">' +
                '<div class="upload-area" onclick="$(this).parent().click()">Choose a file</div>' +
                '<div class="">' +
                '<div class="dropzone-preview-wrapper dropzone-preview-wrapper-' + dropzoneName + '" id="' + previewWrapperId + '"></div>' +
                '<div class="preview-area"></div>' +
                '</div>' +
                '</div>' +
                '<input type="hidden" name="' + actualFieldName + '" value="">' +
                '</div>';
        } else if (fieldType == 'textarea') {
            itemHtml += '<div class="repeater-field col-lg-12 mb-3">' +
                '<label>' + fieldLabel + '</label>' +
                '<textarea name="' + fieldName + '[' + currentIndex + '][' + fieldNameKey + ']" class="form-control" rows="3"></textarea>' +
                '</div>';
        } else if (fieldType == 'select') {
            var optionsHtml = '<option value="" disabled selected>Choose ' + fieldLabel + '</option>';
            for (var j = 0; j < fieldOptions.length; j++) {
                var option = fieldOptions[j];
                var optionId = option.id !== undefined ? option.id : option.value;
                var optionLabel = option.label !== undefined ? option.label : (option.title_en || option.name || option.id || '');
                optionsHtml += '<option value="' + optionId + '">' + optionLabel + '</option>';
            }
            itemHtml += '<div class="repeater-field col-lg-12 mb-3">' +
                '<label>' + fieldLabel + '</label>' +
                '<select name="' + fieldName + '[' + currentIndex + '][' + fieldNameKey + ']" class="ui fluid search dropdown form-control">' +
                optionsHtml +
                '</select>' +
                '</div>';
        } else {
            itemHtml += '<div class="repeater-field col-lg-12 mb-3">' +
                '<label>' + fieldLabel + '</label>' +
                '<input type="text" name="' + fieldName + '[' + currentIndex + '][' + fieldNameKey + ']" class="form-control" value="">' +
                '</div>';
        }
    }
    
    itemHtml += '</div></div>';
    
    itemsContainer.append(itemHtml);
    
    // Get the newly added item
    var newItem = itemsContainer.find('.repeater-item').last();
    
    updateRepeaterIndexes(wrapper);
    
    // Initialize file uploads and dropdowns ONLY for the newly added item (not all items)
    setTimeout(function() {
        // Initialize Semantic UI dropdowns for select fields
        newItem.find('select.ui.dropdown').dropdown();
        
        if (typeof initFilesUpload === 'function') {
            // Only initialize dropzone for the new item's dropzone wrapper
            var newItemDropzone = newItem.find('.dropzone-upload-wrapper');
            if (newItemDropzone.length && !newItemDropzone.hasClass('dz-clickable') && !newItemDropzone.data('dropzone')) {
                // Manually initialize dropzone for just this new element
                $.get('/cms/file-preview-template', {}, function(data) {
                    newItemDropzone.each(function() {
                        var elem = $(this);
                        if (elem.hasClass('dz-clickable') || elem.data('dropzone')) {
                            return; // Already initialized
                        }
                        
                        var name = elem.attr('name');
                        var multiple = elem.attr('is_multiple');
                        var previewId = elem.attr('data-preview-id');
                        var previewarea = elem.find('.preview-area');
                        
                        var previewsContainer;
                        if (previewId) {
                            var previewElement = document.getElementById(previewId);
                            if (!previewElement) {
                                var wrapper = $('<div class="dropzone-preview-wrapper dropzone-preview-wrapper-' + name + '" id="' + previewId + '"></div>');
                                elem.find('.dropzone-preview-wrapper').replaceWith(wrapper);
                                previewElement = document.getElementById(previewId);
                            } else {
                                $(previewElement).addClass('dropzone-preview-wrapper-' + name);
                            }
                            previewsContainer = previewElement;
                        } else {
                            previewsContainer = '.dropzone-preview-wrapper-' + name;
                        }
                        
                        elem.dropzone({
                            url: "/cms/upload",
                            paramName: "file",
                            addRemoveLinks: true,
                            previewsContainer: previewsContainer,
                            chunking: true,
                            method: "POST",
                            maxFilesize: 500000,
                            chunkSize: 500000,
                            parallelChunkUploads: false,
                            previewTemplate: data,
                            init: function() {
                                var actualFieldName = elem.attr('data-actual-field-name');
                                this.on("sending", function(file, xhr, formData) {
                                    formData.append("input_name", name);
                                    formData.append("is_multiple", multiple || "false");
                                });
                                this.on("success", function(file, response) {
                                    $(file.previewElement).remove();
                                    if (actualFieldName) {
                                        var responseHtml = $('<div>').html(response);
                                        responseHtml.find('input[type="hidden"]').each(function() {
                                            var currentName = $(this).attr('name');
                                            if (currentName && currentName.indexOf('tmp_') === 0) {
                                                $(this).attr('name', 'tmp_' + actualFieldName);
                                            }
                                        });
                                        response = responseHtml.html();
                                        var hiddenInput = elem.siblings('input[type="hidden"][name="' + actualFieldName + '"]');
                                        if (hiddenInput.length) {
                                            var fileValue = responseHtml.find('input[type="hidden"]').val();
                                            if (fileValue) {
                                                hiddenInput.val(fileValue);
                                            }
                                        }
                                    }
                                    if(multiple === "true"){
                                        previewarea.append(response);
                                    } else {
                                        previewarea.html(response);
                                    }
                                });
                                this.on("error", function(file, errorMessage) {
                                    console.error("Dropzone error:", errorMessage);
                                    alert("Error uploading file: " + errorMessage);
                                });
                            }
                        });
                    });
                });
            }
        }
    }, 100);
};

window.removeRepeaterItem = function(btn) {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded. Cannot remove repeater item.');
        return false;
    }
    
    if (confirm('Are you sure you want to remove this item?')) {
        $(btn).closest('.repeater-item').remove();
        var wrapper = $(btn).closest('.repeater-wrapper');
        updateRepeaterIndexes(wrapper);
    }
};

window.updateRepeaterIndexes = function(wrapper) {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded. Cannot update repeater indexes.');
        return false;
    }
    
    wrapper.find('.repeater-item').each(function(index) {
        $(this).attr('data-index', index);
        $(this).find('.repeater-item-title').text('Item #' + (index + 1));
        
        // Update all input names with new index
        $(this).find('input, textarea, select').each(function() {
            var name = $(this).attr('name');
            if (name) {
                var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                $(this).attr('name', newName);
            }
        });
        
        // Update dropzone names and preview IDs - handle bracket-free notation
        $(this).find('.dropzone-upload-wrapper').each(function() {
            var $dz = $(this);
            var oldName = $dz.attr('name');
            var oldActualName = $dz.attr('data-actual-field-name');
            
            if (oldName && oldName.indexOf('_') >= 0 && oldName.indexOf('upld_') === 0) {
                // Extract field name, index, and sub-field name from bracket-free name
                // Format: upld_cards_0_image -> cards, 0, image
                var parts = oldName.replace('upld_', '').split('_');
                if (parts.length >= 3) {
                    var fieldName = parts[0];
                    var oldIndex = parts[1];
                    var subFieldName = parts.slice(2).join('_');
                    
                    // Create new bracket-free name with updated index
                    var newName = 'upld_' + fieldName + '_' + index + '_' + subFieldName;
                    var newActualName = fieldName + '[' + index + '][' + subFieldName + ']';
                    var newPreviewId = 'preview-' + fieldName + '-' + index + '-' + subFieldName;
                    
                    $dz.attr('name', newName);
                    $dz.attr('data-actual-field-name', newActualName);
                    $dz.attr('data-preview-id', newPreviewId);
                    $dz.find('.dropzone-preview-wrapper').attr('id', newPreviewId);
                    
                    // Update the hidden input name
                    var hiddenInput = $dz.siblings('input[type="hidden"]');
                    if (hiddenInput.length) {
                        hiddenInput.attr('name', newActualName);
                    }
                }
            }
        });
    });
};

// Aliases removed: global function declarations overwrote window.addRepeaterItem
// and caused infinite recursion. Use window.addRepeaterItem etc. directly.
</script>

