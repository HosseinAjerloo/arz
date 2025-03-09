@extends('Panel.Layout.master')
@section('content')
    <section class="errors">

    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/checked.svg')}}" alt="" class="w-5 h-5">
            <h1 class="text-sm font-bold">
                خرید با موفقیت انجام شد
            </h1>
        </header>
        <article class="flex flex-col justify-center space-y-3 p-2 ">
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">کد رهگیری :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <img src="{{asset('merikhArz/src/images/copy.svg')}}" alt="" class="w-4 h-4">
                    <p class="flex items-center justify-center text-mini-base">{{$transitionDelivery->payment_batch_num??''}}</p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">تاریخ :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        1403/9/10 22:15
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">شماره خرید :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{$transitionDelivery->finance_id??''}}
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">مبلغ حواله :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{$transitionDelivery->payment_amount??''}}
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">آدرس حساب مقصد :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{$transitionDelivery->payee_account}}
                    </p>
                </div>
            </div>
        </article>
        <div class="flex justify-center items-center py-3">
            <a href="{{route('panel.index')}}" class="bg-gradient-to-b from-DE9408 to-FFB01B flex items-center text-mini-mini-base px-4 py-2.5 text-white rounded-lg">
                بازگشت به پنل کاربری

            </a>
        </div>
    </section>


@endsection

