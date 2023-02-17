<div style="width: 100%; overflow: auto">
    
   
    <table class="ui compact table  ">
        <thead>

            @if($data['display_title'])
            <tr><th colspan="{{count($data['columns'])}}">
                {{$gadget->title}}
              </th>
            </tr>
            @endif
            @if($data['display_header'])
          <tr>
              @foreach($data['columns'] as $column)
              <th>{{$column['label'] }}</th>
              @endforeach
          </tr>
          @endif

          
        </thead>
        <tbody>
            @foreach($data["rows"] as $row)
            
                <tr>
                    @foreach($data['columns'] as $column)
                        <td>{{$row[$column['key']] }}</td>
                    @endforeach
                </tr>
          @endforeach
          
        </tbody>
      </table>
    </div>