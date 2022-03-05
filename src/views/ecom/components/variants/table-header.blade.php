<thead>
    <tr>
        <th style="min-width: 110px !important">Variant</th>
        @if(!isset($type) || ( isset($type) && $type != "new"))
        <th style="min-width: 90px !important">Hide</th>
        <th style="min-width: 90px !important">Image</th>
        <th style="min-width: 70px !important">SKU</th>
        <th style="min-width: 70px !important" >Barcode</th>
        @endif
        <th style="min-width: 55px !important; width: 80px !important; text-align:center">Stock</th>
        <th style="min-width: 55px !important; width: 80px !important; text-align:center">Price</th>
        <th style="min-width: 55px !important; width: 80px !important; text-align:center">Discount</th>
        <th style="min-width: 55px !important; width: 80px !important; text-align:center">Cost</th>
      
        @if(!isset($deletable) || (isset($deletable) && $deletable))
        <th style="min-width: 65px !important;width: 65px !important; text-align:center">Action</th>
        @endif
      </tr>
  </thead>