@extends('Auth.Layout.master')

@section('header-content')
<section class="h-14 flex items-center justify-center px-4">
    <div class="flex items-center space-x-reverse space-x-2">
        <p class="text-base font-black ">
           ویرایش کلمه عبور
        </p>
    </div>


</section>
@endsection
@section('content')
<form class="flex flex-col justify-center items-center space-y-6 py-2 pb-6  border-b border-black/40" method="POST" action="{{route('post.update.Password',$otp->token)}}">
    @csrf

    <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
        <img src="{{asset('merikhArz/src/images/key.svg')}}" alt="" class="w-6 h-6">
        <input type="password"
               class="w-full h-full py-1.5 outline-none px-2  placeholder:text-center  placeholder:text-sm"
               placeholder="کلمه عبور " name="password">
    </div>
    <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
        <img src="{{asset('merikhArz/src/images/key.svg')}}" alt="" class="w-6 h-6">
        <input type="password"
               class="w-full h-full py-1.5 outline-none px-2  placeholder:text-center  placeholder:text-sm"
               placeholder="تکرار کلمه عبور" name="password_confirmation">
    </div>


    <button class="bg-gradient-to-b from-FFB01B to-DE9408 text-sm w-52 h-10 rounded-md text-white font-bold">ورود به
            ویرایش کلمه عبور
    </button>

</form>
@endsection
