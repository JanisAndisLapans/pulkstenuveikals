<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{!! csrf_token() !!}"/>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <title>{{__('listing.title')}}</title>
</head>
<body>
<x-title-menu/>
<div class = "container">
    <div class="row">
        <div id="listingHolder" class="col-10 border border-4 border-info border-info">
            @foreach($items->chunk(3) as $row_items)
                <div class="row">
                    @foreach($row_items as $item)
                        @if($item->active)
                            <div class="col" onclick="window.location.href = '/product/' + '{{$item->slug}}' " style="cursor: pointer; flex: 0 0 33%">
                                <div class="row border border-4 border-dark bg-light" style="word-wrap:break-word; height: 550px" >
                                    <div class="col-4 w-100">
                                        <img src="/{{$item->image}}" style="height: 350px; width: 270px"/>
                                    </div>
                                    <div class="col-2 w-100">
                                        <div class="text-center">
                                            <h4 class="text-warning text-decoration-underline">{{$item->name}}</h4>
                                        </div>
                                        <div class="text-center">
                                            <h5 class="text-warning text-decoration-underline">{{$item->price}}€</h5>
                                        </div>
                                        <div class="overflow-hidden">
                                            @if(strlen($item->desc)>=160)
                                                {{substr_replace($item->desc, "...", 150, 9999999)}}
                                            @else
                                                {{$item->desc}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @for($i = 0; $i<3-$row_items->count(); $i++)
                        <div class="col invisible">
                            <div class="row border border-4 border-dark bg-light" style="word-wrap:break-word">
                                <div class="col-4 w-100">
                                    <img style="height: 350px; width: 270px"/>
                                </div>
                                <div class="col-2 w-100">
                                    <div class="text-center">
                                        <h4 class="text-warning text-decoration-underline"></h4>
                                    </div>
                                    <div class="text-center">
                                        <h5 class="text-warning text-decoration-underline">€</h5>
                                    </div>
                                    <div class="overflow-hidden">
                                        @if(strlen($item->desc)>150)
                                            {{substr_replace($item->desc, "...", 150, 9999999)}}
                                        @else
                                            {{$item->desc}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            @endforeach
        </div>
        <div class="col-2 bg-dark bg-opacity-50 text-center">
            <form id="filterForm">
            @csrf
            <div class="mt-3">
                <buton id="filterSub" type="button" class="btn btn-success text-uppercase">{{__('listing.filter')}}</buton>
            </div>
            <div class="mt-3">
                <label class="form-label"><h5>{{__('listing.product_name')}}: </h5></label> <br>
                <input class="form-control" name="name" id="name"/>
            </div>
            <div class="mt-3">
                <label class="form-label"><h5>{{__('listing.product_price')}}: </h5></label> <br>
                <div class="row">
                    {{__('listing.from')}}
                    <input  class = "form-control h-25" type="text" name="lowPrice" id="lowPrice" readonly style=" color:#f6931f; font-weight:bold; width: 40%">
                    {{__('listing.to')}}
                    <input  class = "form-control h-25" type="text" name="highPrice" id="highPrice" readonly style=" color:#f6931f; font-weight:bold; width: 40%">
                </div>
                <br>
                <div id="slider-range"></div>
                <br>
                <div id="slider-rangeSmall"></div>

                <div class="mt-1">
                    <label class="form-check-label">{{__('listing.newest')}}</label>
                    <input id="new" name="sort"  type="radio" value="new" checked><br>
                    <label class="form-check-label">{{__('listing.cheapest')}}</label>
                    <input id="cheap" name="sort"  type="radio" value="cheap"><br>
                    <label class="form-check-label">{{__('listing.expensive')}}</label>
                    <input id="expensive" name="sort" type="radio" value="expensive">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label"><h5>{{__('listing.categories')}}:</h5></label> <br>
            </div>
            <div class="mt-1">
                @foreach($categories as $cateParent => $cates)
                    @php($stripped = str_replace(' ','',$cateParent))
                    <button class="btn btn-link  text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->iteration}}" aria-expanded="false" aria-controls="collapse{{$loop->iteration}}">
                        {{$cateParent}}
                    </button>
                    <div class="collapse" id="collapse{{$loop->iteration}}">
                        <div class="card card-body">
                            <div class="form-check">
                                <label class="form-check-label">Visi</label>
                                <input class="{{$stripped}}All" name="{{$stripped}}All" type="checkbox" class="form-check-input" checked/>
                                @foreach($cates as $cate)
                                    <label class="form-check-label">{{$cate}}</label>
                                    <input class="{{$stripped}}" name="{{str_replace([' ', ',', '.', "'"],'',$cate)}}" type="checkbox" class="form-check-input" checked/>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function (){
                            $(".{{$stripped}}All").change(function (){
                                $(".{{$stripped}}").prop("checked", this.checked);
                            });
                            $(".{{$stripped}}").change(function(){
                                if($(".{{$stripped}}All").prop("checked")){
                                    $(".{{$stripped}}All").prop("checked", false);
                                }
                            });
                        });
                    </script>
                @endforeach
                </form>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function()
    {
        $("#filterSub").on('click', function(){
            $.ajaxSetup({
                headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
            });

            $.ajax({
                url : '{{action([App\Http\Controllers\ProductController::class, 'indexListingFilter'])}}',
                type:'POST',
                data:{
                    '_token' : '<?php echo csrf_token() ?>',
                    'name' : $("input[name=name]").val(),
                    'lowPrice' : $("input[name=lowPrice]").val(),
                    'highPrice' : $("input[name=highPrice]").val(),
                    @foreach($categories as $cateParent => $cates)
                        @php($stripped = str_replace([' ', ',', '.', "'"],'',$cateParent))
                        'All{{$stripped}}' : $("input[name={{$stripped}}All]:checked").val(),
                    @foreach($cates as $cate)
                        @php($cstripped = str_replace([' ', ',', '.', "'"],'',$cate))
                        'Cate{{$cstripped}}' : $("input[name={{$cstripped}}]:checked").val(),
                    @endforeach
                    @endforeach
                },
                success:function(data) {
                    let listingHolder = $("#listingHolder");
                    const card = `
                                    <div class="col" onclick="window.location.href = '/product/{slug}' " style="cursor: pointer; flex: 0 0 33%">
                                    <div class="row border border-4 border-dark bg-light" style="word-wrap:break-word; height: 550px">
                                        <div class="col-4 w-100">
                                            <img src="/{image}" style="height: 350px; width: 270px"/>
                                        </div>
                                        <div class="col-2 w-100">
                                            <div class="text-center">
                                                <h4 class="text-warning text-decoration-underline">{name}</h4>
                                            </div>
                                            <div class="text-center">
                                                <h5 class="text-warning text-decoration-underline">{price}€</h5>
                                            </div>
                                            <div class="overflow-hidden">
                                            {desc}
                        </div>
                    </div>
                </div>
            </div>
 `
                    let toAdd = "";
                    let items = $.map(data.items, function(value, key){
                        return value
                    });
                    if($('#new').prop("checked")){
                        items.sort((a,b) => Date.parse(a.created_at)>Date.parse(b.created_at) ? -1 : 1);
                    }
                    else if($('#cheap').prop("checked")){
                        items.sort(function(a,b) {
                            let aprice = parseFloat(a.price);
                            let bprice = parseFloat(b.price);
                            return aprice < bprice ? -1 : 1;
                        });
                    }
                    else if($('#expensive').prop("checked")){
                        items.sort(function(a,b) {
                            let aprice = parseFloat(a.price);
                            let bprice = parseFloat(b.price);
                            return aprice > bprice ? -1 : 1;
                        });
                    }
                    let realInd = 0;
                    for(let i = 0;i<items.length;i++)
                    {
                        let item = items[i];
                        if(!item.active){
                            continue;
                        }
                        if(realInd%3==0){
                            toAdd += '<div class="row">';
                        }
                        let desc = item.desc;
                        if(desc.length>150)
                        {
                            desc = desc.substring(0,147) + '...';
                        }
                        toAdd += card.replace('{slug}', item.slug).replace('{image}', item.image).replace('{name}', item.name).
                        replace('{price}', item.price).replace('{desc}', desc);
                        if(realInd%3==2){
                            toAdd += '</div>';
                        }
                        realInd++;
                    }
                    listingHolder.html(toAdd);
                },
            });
        });

        $("#lowPrice").val(0);
        $("#highPrice").val(50000);

        $(function () {
            $("#slider-range").slider({
                step: 100,
                range: true,
                min: 0,
                max: 49000,
                values: [0, 49000],
                slide: function (event, ui) {
                    $("#lowPrice").val(ui.values[0] + $("#slider-rangeSmall").slider("values")[0]);
                    $("#highPrice").val(ui.values[1] + $("#slider-rangeSmall").slider("values")[1]);
                }
            });

        });

        $(function () {
            $("#slider-rangeSmall").slider({
                step: 10,
                range: true,
                min: 0,
                max: 1000,
                values: [0, 1000],
                slide: function (event, ui) {
                    $("#lowPrice").val(ui.values[0] + $("#slider-range").slider("values")[0]);
                    $("#highPrice").val(ui.values[1] + $("#slider-range").slider("values")[1]);
                }
            });
        });
    });
</script>
</body>
</html>
