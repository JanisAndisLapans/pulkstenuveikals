<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
        <title>Pulksteņu veikals</title>
    </head>
    <body>
        <x-title-menu/>
        <div class = "container">
            <div class="row">
                <div class="col-10 border border-4 border-info border-info">
                    @foreach($items->chunk(3) as $row_items)
                            <div class="row">
                            @foreach($row_items as $item)
                                @if($item->active)
                                <div class="col" onclick="window.location.href = '/product/' + '{{$item->slug}}' " style="cursor: pointer;">
                                    <div class="row border border-4 border-dark bg-light" style="word-wrap:break-word">
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
                    <form method="POST"
                           action="{{action([App\Http\Controllers\ProductController::class, 'indexListingFilter'])}}" >
                        @csrf
                        <div class="mt-3">
                            <input type="submit" class="btn btn-success" value = "ATLASĪT"/>
                        </div>
                        <div class="mt-3">
                            <label class="form-label"><h5>Nosaukums precei:</h5></label> <br>
                            <input class="form-control" name="name" id="name"/>
                        </div>
                        <div class="mt-3">
                            <label class="form-label"><h5>Cena:</h5></label> <br>
                            <div class="row">
                                no
                                <input  class = "form-control h-25" type="text" name="lowPrice" id="lowPrice" readonly style=" color:#f6931f; font-weight:bold; width: 40%">
                                līdz
                                <input  class = "form-control h-25" type="text" name="highPrice" id="highPrice" readonly style=" color:#f6931f; font-weight:bold; width: 40%">
                            </div>
                            <br>
                            <div id="slider-range"></div>
                            <br>
                            <div id="slider-rangeSmall"></div>

                            <div class="mt-1">
                                <label class="form-check-label">Jaunākās vispirms</label>
                                <input name="sort"  type="radio" value="new" checked>
                                <label class="form-check-label">Lētākas vispirms</label>
                                <input name="sort"  type="radio" value="cheap">
                                <label class="form-check-label">Dārgākās vispirms</label>
                                <input name="sort" type="radio" value="expensive">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label"><h5>Kategorijas:</h5></label> <br>
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
                                                <input class="{{$stripped}}" name="{{str_replace(' ','',$cate)}}" type="checkbox" class="form-check-input" checked/>
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

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $( "#lowPrice" ).val(0);
            $( "#highPrice" ).val(50000);

            $( function() {
                $( "#slider-range" ).slider({
                    step: 100,
                    range: true,
                    min: 0,
                    max: 49000,
                    values: [ 0, 49000 ],
                    slide: function( event, ui ) {
                        $( "#lowPrice" ).val(ui.values[0] + $( "#slider-rangeSmall" ).slider("values")[0]);
                        $( "#highPrice" ).val(ui.values[1] + $( "#slider-rangeSmall" ).slider("values")[1]);
                    }
                });

            } );

            $( function() {
                $( "#slider-rangeSmall" ).slider({
                    step: 10,
                    range: true,
                    min: 0,
                    max: 1000,
                    values: [ 0, 1000 ],
                    slide: function( event, ui ) {
                        $( "#lowPrice" ).val(ui.values[0] + $( "#slider-range" ).slider("values")[0]);
                        $( "#highPrice" ).val(ui.values[1] + $( "#slider-range" ).slider("values")[1]);
                    }
                });
            } );
        </script>
    </body>
</html>
