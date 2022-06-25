<div>
    @if($errors->count()>0)
        <div class="bg-danger opacity-75 border border-4 border-danger rounded p-3 w-75">
            @foreach($errors->all() as $error)
                {{$error}}<br>
            @endforeach
        </div>
    @endif
</div>
