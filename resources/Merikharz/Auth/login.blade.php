@extends('Auth.Layout.master')

@section('header-content')
<section class="h-14 flex items-center justify-center px-4">
    <div class="flex items-center space-x-reverse space-x-2">
        <p class="text-base font-black ">
            ورود به حساب کاربری
        </p>
    </div>


</section>
@endsection
@section('content')
<form class="flex flex-col justify-center items-center space-y-6 py-2 pb-6  border-b border-black/40" method="POST" action="{{route('login.simple-post')}}">
    @csrf
    <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
        <img src="{{asset('merikhArz/src/images/svgPhone.svg')}}" alt="" class="w-6 h-6">
        <input type="text"
               class="w-full h-full py-1.5 outline-none px-2 placeholder:text-center placeholder:text-sm"
               placeholder="شماره همراه  (*********09)" name="mobile">
    </div>
    <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
        <img src="{{asset('merikhArz/src/images/key.svg')}}" alt="" class="w-6 h-6">
        <input type="password"
               class="w-full h-full py-1.5 outline-none px-2  placeholder:text-center  placeholder:text-sm"
               placeholder="کلمه عبور " name="password">
    </div>

    <div class="flex items-center justify-between space-x-reverse space-x-2 w-full">
        <div class="flex items-center space-x-reverse space-x-2">
            <input type="checkbox" class="py-1.5 outline-none px-2 " name="rememberMe">
            <label class="text-mini-base font-bold">مرا به خاطر بسپار</label>
        </div>
        <div>
            <a href="{{route('forgotPassword')}}" class="text-mini-base text-sky-600 underline underline-offset-8">فراموشی رمز عبور</a>
        </div>
    </div>
    <div class="flex items-center justify-between space-x-reverse space-x-1.5 w-full">
        <div class="flex items-center space-x-reverse space-x-2">
            <p class="text-mini-base">هنوز ثبت نام نکرده اید</p>
            <a class="text-mini-base text-sky-600 underline underline-offset-8" href="{{route('register')}}">ثبت نام</a>
        </div>
    </div>
    <button class="bg-gradient-to-b from-FFB01B to-DE9408 text-sm w-52 h-10 rounded-md text-white font-bold">ورود به
        حساب کاربری
    </button>

    <a class="text-sm w-52 h-10 rounded-md text-white font-bold bg-black flex items-center justify-center space-x-reverse space-x-2" >
        <img src="../../src/images/google.svg" alt="">
        <span>
             ورود سریع با گوگل
            </span>
    </a>
</form>
@endsection
