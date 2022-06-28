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
    <title>{{__('cart.cart')}}</title>
</head>
<body>
    <x-title-menu/>
    <div class="container">
    @foreach($items as $item)
            <div class="row align-items-center">
                <div class="col-4">
                    <image src="/{{$item->image}}" class="w-100"/>
                </div>
                <div class="col-2">
                    <h2><b class="text-success">{{$item->name}}</b></h2>
                </div>
                <div class="col-2">
                    <h2> {{__('cart.count')}}: {{$item->count}}</h2>
                </div>
                <div class="col-2">
                    <h2 class="text-success"> {{number_format($item->price*$item->count,2,'.', '')}}€</h2>
                </div>
                <div class="col-2">
                    <form method="POST"
                          action="{{action([App\Http\Controllers\CartController::class, 'removeFromCart'])}}">
                        @csrf
                        @method('delete')
                        <input id="id" name="id" value="{{$item->id}}" hidden/>
                        <input class="btn btn-danger text-uppercase text-s h3" type="submit" value="{{__('cart.delete')}}">
                    </form>
                </div>
            </div>
    @endforeach
    </div>
    <div class="text-center" style="font-size:30px">
        <Button aria-controls="collapse" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#collapse" class="btn btn-success text-uppercase w-25"
        @if(!isset($totalPrice)) hidden @endif>{{__('cart.buy')}}</Button>
        {{__('cart.total')}} :@if(isset($totalPrice)) {{number_format($totalPrice, 2, '.', '')}} @else 0 @endif €
    </div>
    <div id="collapse" class="collapse @if(isset($errors) && $errors->count()>0) show @endif">
        <div class="position-fixed top-0 stat-0 bg-black opacity-50 vh-100 vw-100"></div>
        <div class="bg-white position-fixed bottom-0 w-50 h-75 rounded-top border-top border-start border-end border-5 border-info" style="left: 25%">
                <div aria-controls="collapse" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#collapse"
                     class="border border-danger border-2 text-danger ms-auto me-2 mt-2 text-center" style="width: 20px; cursor:pointer">x</div>
                <div class="ms-4 mt-5 overflow-scroll position-relative w-100 h-100">
                    @if(isset($errors)) <x-error-display :errors="$errors"/> @endif
                    @php
                        $itemcounts = [];
                        foreach($items as $item)
                        {
                            $itemcounts[$item->id] = $item->count;
                        }
                        $itemcounts = json_encode($itemcounts);
                    @endphp
                    <form id="payForm" method="POST"
                          action="cart/order/{{$itemcounts}}">
                        @csrf
                    <div class="text-center fw-bold" style="font-size: 20px">
                        {{__('cart.order_addr')}}
                    </div>
                    <label class="fw-bold"> {{__('cart.email')}}* : </label>
                    <input name="email" id="email" class="form-control w-75" value = "{{old('email')}}" /> <br>
                    <label class="fw-bold"> {{__('cart.phone')}}* : </label>
                    <input type="number" name="phone" id="phone" class="form-control w-75" value = "{{old('phone')}}" /> <br>
                    <label class="fw-bold"> {{__('cart.city')}}* : </label>
                    <input name="city" id="city" class="form-control w-75" value = "{{old('city')}}"/> <br>
                    <label class="fw-bold"> {{__('cart.street')}}* : </label>
                    <input name="street" id="street" class="form-control w-75" value = "{{old('street')}}"/> <br>
                    <label class="fw-bold"> {{__('cart.apartament')}} : </label>
                    <input name="apartament" id="apartament" class="form-control w-75" value = "{{old('apartament')}}"/> <br>
                    <label class="fw-bold"> {{__('cart.zip')}}* : </label>
                    <input name="zip" id="zip" class="form-control w-75" value = "{{old('zip')}}"/> <br>
                </form>
                <div class="text-center fw-bold" style="font-size: 20px">
                    {{__('cart.payment_card')}}
                </div>
                <select id="cards">
                    <option value="Visa">Visa</option>
                    <option value="Mastercard">Mastercard</option>
                </select>
                <br>
                <label class="fw-bold"> {{__('cart.card_owner')}}* : </label>
                <input id="cardholder" class="form-control w-75"/> <br>
                <label class="fw-bold"> {{__('cart.number')}}* : </label>
                <input id="cnumber" class="form-control w-75"/> <br>
                <label class="fw-bold"> CVC* : </label>
                <input id="cvc" class="form-control w-75"/> <br>
                <div class="mb-5"/>
                <div class="text-center">
                    <button type="submit" class="btn btn-success text-uppercase" form="payForm">{{__('cart.pay')}}</button>
                </div>
                </div>
        </div>
</body>
</html>
