@extends('Panel.Layout.master')
@section('content')

    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset("merikhArz/src/images/closeRedIcon.svg")}}" alt="" class="w-5 h-5">
            <h1 class="text-sm font-bold">
                خرید ناموفق
            </h1>
        </header>
        <article class="flex flex-col p-2 space-y-2 ">
            <div class="flex  ">
                @if($invoice->bank_id)
                    <p class="text-mini-base leading-loose space-x-reverse space-x-2">
                        <img src="{{asset("merikhArz/src/images/closeRedIcon.svg")}}" alt=""
                             class=" inline-block w-6 h-6">
                        کیف پول شما به مبلغ 120 هزار تومان شارژ شده است.به دلیل <span
                            class="font-bold text-rose-700">اختلال</span> به وجود آمده در انجام حواله پرفکت
                        مانی لطفا در ساعات آینده با استفاده از کیف پول خود نسبت به انجام تراکنش اقدام نمایید.
                    </p>
                @else
                    <p class="text-mini-base leading-loose space-x-reverse space-x-2">
                        <img src="{{asset("merikhArz/src/images/closeRedIcon.svg")}}" alt=""
                             class=" inline-block w-6 h-6">
                        به دلیل <span class="font-bold text-rose-700">اختلال</span>
                        به وجود آمده در انجام حواله پرفکت
                       روندکاهش مبلغ از کیف پول شما متوقف شد و شما میتوانیدباگذشت زمان دوباره تلاش فرمایید باتشکر.
                    </p>
                @endif
            </div>
            <div class="flex space-x-reverse space-x-2 ">
                <img src="{{asset('merikhArz/src/images/wallet.svg')}}" alt="" class=" inline-block w-6 h-6">
                <p class="text-mini-base leading-loose">
                    موجودی فعلی کیف پول : <span class="font-bold text-green-600">
                        {{numberFormat(empty(substr($balance,0,(strlen($balance)-1)))?0: substr($balance,0,(strlen($balance)-1)))}}</span>  تومان
                </p>
            </div>
            <div class="flex  mt-6 ">
                <a href="{{route('panel.index')}}"
                   class="bg-gradient-to-b from-DE9408 to-FFB01B flex items-center text-mini-mini-base px-4 py-2.5 text-white rounded-lg">
                    بازگشت به پنل کاربری

                </a>
            </div>

        </article>
    </section>

@endsection
