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
    <script src="{{ URL::asset('js/jquery.star-rating-svg.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/star-rating-svg.css') }}">
    <title>{{$item->name}}</title>
</head>
    <body>
    <x-title-menu/>
    @php
        $avg = 0;
        if($reviews->count()>0){
        foreach($reviews as $rew)
        {
            $avg += $rew->stars;
        }
        $avg/=$reviews->count();
    }
    @endphp
        @if(session()->has("addedToCart"))
            <script>
                $( function() {
                    $( "#dialog-confirm" ).dialog({
                        resizable: false,
                        height: "auto",
                        width: 400,
                        modal: true,
                        buttons: {
                            "{{__('product.stay')}}": function() {
                                $( this ).dialog( "close" );
                            },
                            "{{__('product.go_to_cart')}}": function() {
                                window.location.href = "/cart";
                            }
                        },
                        open: function(event, ui){
                            setTimeout("$('#dialog-confirm').dialog('close')",5000);
                        }
                    });
                } );
            </script>
            <div class="position-absolute top-0" id="dialog-confirm">
                <p><span style="float:left; margin:12px 12px 20px 0;"></span>{{__('product.successfully_added_to_cart')}}</p>
            </div>
        @endif

        <div class="container mt-4">
            <div class="row">
                <div class="col-4">
                    <img class="w-100" src="/{{$item->image}}">
                </div>
                <div class="col-7">
                    <div>
                        <span style="font-size: 30px" class="text-decoration-underline fw-bold">{{$item->name}}</span>
                        <br>
                        @if($reviews->count()>0)
                            <span class="rating"></span>
                        @else
                            <span style="color:gray">({{__('product.not_rated')}})</span>
                        @endif
                    </div>
                    {{$item->desc}}
                    <br>
                    <form class="float-end" method="POST"
                          action="{{action([App\Http\Controllers\CartController::class, 'addToCart'], $item->slug)}}" >
                        @csrf
                        <input type="submit" class="btn btn-success mt-3" value="{{__('product.buy')}} {{$item->price}}???"/>
                        <input type="text" name="id" id="id" value="{{$item->id}}" hidden>
                        <input type="number" name="numberOf" id="numberOf" class="form-control" style="width: 80px" value="1">
                    </form>
                </div>
            </div>
        </div>
            <div class="w-50 mx-auto">
                @if(count($categories)>0)
                    <table class="table table-striped">
                        @foreach($categories as $parent => $children)
                            <tr>
                               <td>
                                   <b>{{$parent}}</b>
                               </td>
                               <td>
                                   @foreach($children as $cate)
                                       @if($loop->last)
                                           {{$cate}}
                                       @else
                                           {{$cate}},
                                       @endif
                                   @endforeach
                               </td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
            <div  class="mt-3 container">
                <div class="row">
                    <div class="col-6">
                        <h3 class="text-success">{{__('product.user_inqs')}}: </h3>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-success text-white"
                                aria-controls="collapseInq" aria-expanded="false" data-bs-target="#collapseInq" data-bs-toggle="collapse">
                            {{__('product.add_inq')}}</button>
                    </div>
                </div>
            </div>
            <div id="collapseInq" class="collapse">
                <form method="POST"
                      action="{{action([App\Http\Controllers\InquiryController::class, 'ask'], $item->id)}}">
                    @csrf
                    <textarea name="question" id="question" rows="5" cols="80" class="form-control w-50" maxlength="200"></textarea>
                    <input style="font-size: 20px" type="submit" class="btn btn-warning text-white" value="{{__('product.inquire')}}"/>
                    {{__('product.remaining_chars')}}: <span class="characterCount">200</span>
                </form>
            </div>
            <hr>
            @foreach($inquiries as $inq)
                <div class="container">
                    <div class="row">
                        <div class="col-auto">
                            <small style="color: grey;">{{__('product.question')}} {{$inq->updated_at}}</small><br>
                            <div  style="width : 25vw; word-wrap: break-word">
                                {{$inq->question}}
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-success text-white"
                                    aria-controls="collapseAns{{$inq->id}}" aria-expanded="false" data-bs-target="#collapseAns{{$inq->id}}" data-bs-toggle="collapse">
                                {{__('product.add_ans')}}</button>
                        </div>
                    </div>
                </div>
                <div id="collapseAns{{$inq->id}}" class="collapse">
                    <form method="POST"
                          action="{{action([App\Http\Controllers\AnswerController::class, 'answer'], $inq->id)}}">
                        @csrf
                        <textarea name="ans" id="answer{{$inq->id}}" rows="5" cols="80" class="form-control w-50" maxlength="200"></textarea>
                        <script>
                            $('#answer{{$inq->id}}').on('input', function() {
                                $('.characterCount{{$inq->id}}').text(200-$(this).val().length);
                            });
                        </script>
                        <input style="font-size: 14px" type="submit" class="btn btn-warning text-white" value="{{__('product.inquire')}}"/>
                        {{__('product.remaining_chars')}}: <span class="characterCount{{$inq->id}}">200</span>
                    </form>
                </div>
                @if(isset($answers[$inq->id]))
                    @foreach($answers[$inq->id] as $ans)
                        <div style="margin-left: 20vw">
                            <small style="color: grey;">{{__('product.inq_answer')}}: {{$ans->updated_at}}</small><br>
                            <div style="width : 25vw; word-wrap: break-word">{{$ans->ans}}
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
    <div  class="mt-3 container">
        <div class="row">
            <div class="col-6">
                <h3 class="text-success">{{__('product.review')}}: </h3>
            </div>
            <div class="col-2">
                <button class="btn btn-success text-white"
                        aria-controls="collapseReview" aria-expanded="false" data-bs-target="#collapseReview" data-bs-toggle="collapse">
                    {{__('product.add_review')}}</button>
            </div>
        </div>
    </div>

    <div class="collapse @if(session()->has('verifyStart'))show @endif "id="collapseReview">
        <form method="POST" action="{{action([App\Http\Controllers\ReviewController::class, 'startVerification'], $item->id)}}">
            @csrf
            <p>{{__('product.to_add_review_verify_email')}}</p>
            <label>{{__('product.order_nr')}}: </label>
            <input value="{{old('id')}}" type="number" class="form-control" name="id" style="width:120px"> <br>
            <label>{{__('product.email')}}: </label>
            <input class="form-control" id="email" name="email" value="{{old('email')}}" style="width: 400px"/>
            <input type="submit" class="btn btn-warning text-white" value="{{__('product.send')}}" style="font-size: 20px" />
        </form>
    </div>
    <hr>
    @foreach($reviews as $rew)
        <div class="rating{{$rew->id}}" style="margin-left: 10vw"></div>
        <div style="margin-left: 10vw">
            <small style="color: grey;">{{$rew->updated_at}}</small><br>
            <div class="w-50 text-wrap">
                {{$rew->content}}
            </div>
        </div>
        <script>
            $(".rating{{$rew->id}}").starRating({
                initialRating: {{$rew->stars}},
                strokeColor: '#894A00',
                strokeWidth: 10,
                starSize: 25,
                readOnly : true
            });
        </script>
    @endforeach
    <script>
    $(document).ready(function() {
        @if(session('verifyStart') == 'failEmail')
            window.alert("{{session('email')}} {{("product.email_doesn't_belong")}}");
        @elseif(session('verifyStart') == 'failId')
        window.alert("{{("product.product_isnt")}}: {{$item->name}}.");
        @elseif(session('verifyStart') == 'failRepeat')
        window.alert("{{("product.already_rated")}}.");
        @elseif(session('verifyStart') == 'ok')
            window.alert("{{__("product.go_to")}} {{session('email')}}{{__("product.to_verify")}}");
        @endif
        $('#question').on('input', function() {
            $('.characterCount').text(200-$(this).val().length);
        });
        $(".rating").starRating({
            initialRating: {{$avg}},
            strokeColor: '#894A00',
            strokeWidth: 10,
            starSize: 25,
            readOnly : true
        });
    });
    </script>
    </body>
</html>
