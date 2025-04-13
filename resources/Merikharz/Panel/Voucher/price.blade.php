@extends('Panel.layout.master')
@section('content')
    <h1 class="text-center text-3xl m-2 mb-6">خرید اعتبار Utopia</h1>
    <section class="w-full lg:w-[48%] lg:mt-0 flex flex-wrap justify-between mt-4 mx-auto">
        <a href="{{route('voucher')}}"
           class="w-[48%]  bg-black rounded-md flex justify-center items-center flex-col mt-2">
            <img src="{{asset("merikhArz/src/images/utopia-logo.png")}}" alt="" class="object-cover h-28">
            <p class="text-white text-sm m-1">اعتبار یوتوپیا</p>
            <p class="text-yellow-400 text-sm m-1">مبلغ دلخواه</p>
            <button type="button" style="background-color: green"
                    class="text-white w-1/3 rounded-md py-2 px-4 m-2 mx-auto">خرید
            </button>
        </a>
        @foreach($carts as $cart)
            <a href="{{route('voucher',['amount'=>$cart['value']])}}"
               class="w-[48%] bg-black rounded-md flex justify-center items-center flex-col mt-2">
                <img src="{{asset("merikhArz/src/images/utopia-logo.png")}}" alt="" class="object-cover h-28">
                <p class="text-white text-sm m-1">اعتبار {{$cart['value']}} دلاری یوتوپیا
                    : </p>
                <p class="text-yellow-400 text-sm m-1">{{$cart['value'] * $dollar_price}} هزار تومان </p>
                <button type="button" style="background-color: green"
                        class="text-white w-1/3 rounded-md py-2 px-4 m-2 mx-auto">خرید
                </button>
            </a>
        @endforeach
        <p class="m-4"> خرید اعتبار یوتوپیا با مبلغ کمتر از 5 دلار بدون نیاز به احراز هویت و به صورت آنی انجام می
            شود.</p>
        <p class="m-2"> طبق قوانین بانک مرکزی خرید اعتبار یوتوپیا با مبلغ بیش از 5 دلار نیازمند احراز هویت است</p>
    </section>
@endsection
@section('script-tag')

@endsection

