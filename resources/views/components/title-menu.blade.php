<link href="{{ asset('css/app.css') }}" rel="stylesheet">


<div class="position-relative overflow-hidden p-3 p-md-2 m-md-2 text-center bg-overlay"
     style="background-image: url('/backgroundnav.jpg');  background-repeat: no-repeat;   background-size: cover; "
>
    <div class="col-md-5 p-lg-3 mx-auto my-4 ">
        <h1 class="display-4 font-weight-normal text-primary">{{__('title.store_name')}}</h1>
        <p class="lead font-weight-normal text-warning">{{__('title.desc')}}</p>
    </div>
    <div class="product-device box-shadow d-none d-md-block"></div>
    <div class="product-device product-device-2 box-shadow d-none d-md-block"></div>
</div>

<nav class="site-header py-1 bg-black">
    <div class="container d-flex flex-column flex-md-row justify-content-between">
        <a class="py-2 d-none d-md-inline-block" href="/">{{__('title.products')}}</a>
        <a class="py-2 d-none d-md-inline-block" href="/cart">{{__('title.cart')}}</a>
        @can("can_administrate")
            <a style="color:red" class="py-2 d-none d-md-inline-block" href="/admin">Admin</a>
            <form method="POST" action="\logout">
                @csrf
                <input type="submit" style="color:red" class="btn btn-link py-2 d-none d-md-inline-block" value="Iziet"/>
            </form>
        @endcan
        <form method="POST" action="{{action([App\Http\Controllers\LanguageController::class, 'change'])}}">
        @csrf
            <input type="submit" style="color:red" class="btn btn-link py-2 d-none d-md-inline-block" value="{{__('title.swap_lang')}}"/>
        </form>
    </div>
</nav>
