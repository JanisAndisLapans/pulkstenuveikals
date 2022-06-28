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
        <h3 class="text-success">{{__('review.title')}} {{$item->name}}: </h3>
        @if(isset($errors)) <x-error-display :errors="$errors"/> @endif
        <div class="rating"></div>
        <form method="POST"
            action="{{action([App\Http\Controllers\ReviewController::class, 'review'], [$item->id, $orderId, $code])}}"
        >
            @csrf
            <input name="rating" id="ratingNum" value="@if(old('rating')!=null) {{old('rating')}} @else 4 @endif" />
            <hr>
            <textarea name="review" id="review" rows="5" cols="80" class="form-control w-50" maxlength="400">{{old('review')}}</textarea>
            {{__('review.remaining_chars')}}: <span class="characterCount">400</span>
            <input value="{{__('review.rate')}}" type="submit" class="btn btn-warning" style="font-size: 20px"/>
        </form>
    </body>
    <script>
        $(document).ready(function() {
            $(".rating").starRating({
                initialRating: @if(old('rating')!=null) {{old('rating')}} @else 4 @endif ,
                strokeColor: '#894A00',
                strokeWidth: 10,
                starSize: 25,
                disableAfterRate : false

            });

            $(".rating").on('click', function () {
                $('#ratingNum').attr('value', $(this).starRating('getRating'));
            });
            $('#review').on('input', function () {
                $('.characterCount').text(400 - $(this).val().length);
            });
        });
    </script>
</html>
