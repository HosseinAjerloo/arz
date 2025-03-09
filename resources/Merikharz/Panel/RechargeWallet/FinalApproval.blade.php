@extends('Panel.Layout.master')

@section('content')

    <form class=" w-full border-2 border-black/15 rounded-lg mt-4" method="post" action="{{route('panel.wallet.charging.store')}}">
        @csrf
        <header class="flex items-center justify-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/wallet.svg')}}" alt="" class="w-5 h-5">
            <h1 class="text-mini-base font-bold">
                موجودی کیف پول شما
                <span class="@if($user->getCreaditBalance()>0) text-green-600 @else text-rose-700 @endif">
                    {{numberFormat(empty(substr($user->getCreaditBalance(),0,(strlen($user->getCreaditBalance())-1)))?0: substr($user->getCreaditBalance(),0,(strlen($user->getCreaditBalance())-1)))}}
                </span>
            </h1>
        </header>
        <article class="flex flex-col space-y-3 p-2 text-mini-mini-base">
            <div class="flex  justify-between space-x-reverse space-x-2">
               <p class="font-bold text-sm">
                   مبلغ تراکنش :
               </p>
                <p class="font-bold text-sm">
                    {{numberFormat($inputs['price'])}}
                    تومان
                </p>
            </div>
            <div class="flex  justify-between space-x-reverse space-x-2">
                <p class="font-bold text-sm">
                    تاریخ و ساعت :
                </p>
                <p class="font-bold text-sm">

                    {{\Morilog\Jalali\Jalalian::forge($payment->created_at)->format('%A, %d %B %y')}}
                </p>
            </div>
            <div class="flex  justify-between space-x-reverse space-x-2">
                <p class="font-bold text-sm ">
                    وضعیت تراکنش :
                </p>
                <p class="font-bold text-sm text-yellow-500">
                    در انتظار پرداخت
                </p>
            </div>

        </article>
        <button class="flex justify-center items-center mt-2 py-2  w-full">
            <p
               class="px-4  py-3 text-mini-base bg-gradient-to-b from-FFB01B to-DE9408 text-white rounded-lg shadow-lg">
                تایید و پرداخت با درگاه بانکی
            </p>

        </button>
        <input type="hidden" value="{{$inputs['bank_id']}}" name="bank_id">
        <input type="hidden" value="{{$inputs['price']}}" name="bank_id">
    </form>

@endsection
