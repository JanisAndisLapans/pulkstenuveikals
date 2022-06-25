<div class="m-4">
    <form method="POST"
          action="{{action([$controller, 'filter'])}}">
        @csrf
        <foreach>
            @foreach($headers as $header => $type)
                @if($type=='nofill' || $type == 'img') @continue @endif
                <labal>{{$header}}: </labal>
                @if($type == 'boolean')
                    <input value="1" type="checkbox" class="switch-input" name="{{$header}}" id="{{$header}} " {{ old("$header") ? 'checked="checked"' : '' }}/>
                @elseif($type =='float' || $type == 'decimal')
                    <input step="0.01" type="number" name="{{$header}}" id="{{$header}}" value="{{old($header)}}"/>
                @elseif($type == 'integer' || $type == 'bigint')
                    <input type="number" name="{{$header}}" id="{{$header}}" value="{{old($header)}}"/>
                @else
                    <input name="{{$header}}" id="{{$header}}" value="{{old($header)}}"/>
                @endif
            @endforeach

            <input type="submit" class="btn btn-success" value="meklēt"/>
        </foreach>
    </form>
    @if($items->count()>0)
    <table class="border border-dark border-4 text-center">
        <tr class="border border-dark border-2">
            @foreach($headers as $header => $type)
                <th class="bg-success border-end border-3 border-warning fw-bold" style="font-size: 17px">
                    {{$header}}
                </th>
            @endforeach
            @isset($many)
                @foreach($many as $name => $val)
                    <th class="bg-success border-end border-3 border-warning fw-bold" style="font-size: 17px">
                        {{$name}}
                    </th>
                @endforeach
            @endisset
            <th class="bg-success border-end border-3 border-warning fw-bold" style="font-size: 17px">
                Dzēst
            </th>
            <th class="bg-success border-end border-3 border-warning fw-bold" style="font-size: 17px">
                Rediģēt
            </th>
        </tr>
        @foreach($items as $item)
            <tr class="border border-dark border-2">
            @foreach($headers as $header => $type)
                @php
                    $curr = $item->{"$header"};
                    $constraintIs = isset($constraints) && isset($constraints[$header]);
                @endphp
                <td id="{{"$header$item->id"}}" class="{{$header}} p-2 border-end border-1 border-dark">
                    @if($constraintIs)
                       <a href="/admin/{{"$constraints[$header]#id$curr"}}">
                    @endif
                        @if(strlen($curr)>30)
                            {{ substr_replace($curr, "...", 30, 9999999) }}
                        @else
                            {{$curr}}
                        @endif
                    @if($constraintIs)
                       </a>
                    @endif
                </td>
            @endforeach
            @isset($many)
                @foreach($many as $name => $rel)
                    <td class="p-2 border-end border-1 border-dark"><button class="btn btn-link" type="submit" form="{{"$name$item->id"}}">
                    @php($curr= $rel[$item->id])
                    @foreach($curr as $other)
                        @if($loop->first)
                            {{$other->id}}
                        @elseif($loop->index>3)
                            ...,{{$curr->last()->id}}
                            @break
                        @else
                            {{", $other->id"}}
                        @endif
                        @isset($other->prop)
                            {{"($other->prop)"}}
                        @endisset
                    @endforeach
                    </button></td>
                    <form id="{{"$name$item->id"}}" method="POST" action="/admin/{{$name}}/many">
                        @csrf
                        <input name="ids" value="
                        @foreach($curr as $other)
                            @if($loop->first)
                                {{$other->id}}
                            @else
                                {{",$other->id"}}
                            @endif
                            @isset($other->prop)
                                {{"($other->prop)"}}
                            @endisset
                        @endforeach" hidden/>
                    </form>
                @endforeach
            @endisset
                 <td>
                     <form method="POST"
                           action="{{action([$controller, 'destroy'], $item->id)}}">
                     @csrf
                     @method('delete')
                         <input type="submit" class="btn btn-danger" value="dzēst"/>
                     </form>
                 </td>
                <td>
                    <form method="GET"
                          action="{{action([$controller, 'edit'], $item->id)}}">
                        @csrf
                        <input style="color:white" type="submit" class="btn btn-warning" value="rediģēt"/>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    @else
        <h1>Nav ierakstu</h1>
    @endif
    <form method="GET"
                action="{{action([$controller, 'create'])}}">
        @csrf
        <input style="font-size: 20px" type="submit" class="btn btn-success p-2 mt-3" value="Pievienot ierakstu"/>
    </form>
</div>
