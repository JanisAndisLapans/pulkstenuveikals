<div class="m-4">
    <form method="POST" enctype="multipart/form-data"
          action = {{action([$controller, 'update'], $prev->id)}}>
        @csrf
        @method('put')
        @foreach($headers as $header => $type)
            @php
                if(old($header))
                {
                    $isOld = true;
                }
                else $isOld = false;
            @endphp
            @if($type!='nofill')
                <label>{{$header}}: </label>
                @if(isset($constraints) && isset($constraints[$header]))
                    <select name="{{$header}}" id="{{$header}}">
                        <option value="blank"></option>
                        @foreach($constraints[$header] as $choice)
                            <option value="{{$choice->id}}" {{$isOld && old($header)==$choice->id ? 'selected' : ''}} {{!$isOld && $prev->{"$header"}==$choice->id ? 'selected' : ''}}>{{$choice->id}}</option>
                        @endforeach
                    </select>
                @elseif($type == 'integer')
                    <input class="form-control" type="number" name="{{$header}}" id="{{$header}}" value="{{$isOld ? old($header) : $prev->{"$header"} }}"/>
                @elseif($type == 'boolean')
                    <input value="1" type="checkbox" class="switch-input" name="{{$header}}" id="{{$header}} " {{ $prev->{"$header"} ? 'checked="checked"' : '' }}/>
                @elseif($type == 'float' || $type == 'decimal')
                    <input class="form-control" step="0.01" type="number" name="{{$header}}" id="{{$header}}" value="{{$isOld ? old($header) : $prev->{"$header"} }}"/>
                @elseif($type == 'text')
                    <textarea class="form-control" cols="80" rows="5" name="{{$header}}" id="{{$header}}" style="width: 35vw; height: 180px">{{$isOld ? old($header) : $prev->{"$header"} }}</textarea>
                @elseif($type == 'img')
                    <input type="file" accept=".png, .jpg, .jpeg" name="{{$header}}" id="{{$header}}"/>
                    <label>Paturēt veco bildi: </label>
                    <input value="1" type="checkbox" class="switch-input" name="{{$header}}Keep" id="{{$header}}Keep" checked/>
                @else
                    <input class="form-control" name="{{$header}}" id="{{$header}}" value="{{$isOld ? old($header) : $prev->{"$header"} }}"/>
                @endif
                <br>
            @endif
        @endforeach
        @isset($many)
            @foreach($many as $header => $choices)
                @if(substr($header, -4) != 'Prop')
                    <label>{{$header}}: </label>
                    <select id="{{$header}}ManySel">
                        <option value="blank"></option>
                        @foreach($choices as $choice)
                            <option value="{{$choice->id}}">{{$choice->id}}</option>
                        @endforeach
                    </select>
                    @isset($many[$header.'Prop'])
                        <label>{{$many[$header.'Prop']['name']}}: </label>
                        <input id="{{$header}}Prop" type="{{$many[$header.'Prop']['type']}}"/>@endisset
                    <button type="button" style="color:white" class="btn btn-danger" id="{{$header}}Rem">Dzēst pēdējo</button>
                    <br>
                    <input id="{{$header}}" value="{{old($header) ? old($header) : $prev->{"$header"} }}" class="form-control" style="width: 25vw" name="{{$header}}" readonly/>
                    <script>
                        $('#{{$header}}Rem').on('click', function(){
                           let edit = $("#{{$header}}");
                           let val = edit.attr('value');
                           let comma = val.lastIndexOf(',');
                            edit.attr('value', val.substring(0, comma));
                        });
                        $("#{{$header}}ManySel").change(function(){
                            let addVal = this.value;
                            if(addVal=="blank")
                            {
                                return;
                            }
                            let prop = $('#{{$header}}Prop');
                            if(prop.length)
                            {
                                if(prop.val()=="") return;
                                addVal += '{'+prop.val()+'}';
                                prop.val("");
                            }
                            let curr = $("#{{$header}}").attr("value");
                            if(curr.length==0){
                                $("#{{$header}}").attr("value", addVal);
                            }else{
                                $("#{{$header}}").attr("value", curr += ", " + addVal);
                            }
                            $(this).val("blank").change();
                        });
                    </script>
                @endif
            @endforeach
        @endisset
        <input style="font-size: 20px" type="submit" class="btn btn-success p-2 mt-3" value="Mainīt"/>
    </form>
</div>
