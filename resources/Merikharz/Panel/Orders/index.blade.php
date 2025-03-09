@extends('Panel.Layout.master')
@section('content')
    <section class="errors">

    </section>
    @foreach($financeTransactions as $financeTransaction)

    @endforeach
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4 transition-all">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center space-x-2 space-x-reverse">
                <img src="{{asset('merikhArz/src/images/checked.svg')}}" alt="" class="w-6 h-6">
                <h1 class="text-sm">
                    حواله پرکت مانی
                </h1>
            </div>
            <img src="{{asset('merikhArz/src/images/perfectmone.svg')}}" alt="" class="w-6 h-6">
        </header>
        <article class="flex flex-col justify-start space-y-4 p-2 orderParent transition-all">
            <div>
                <div class="flex items-center space-x-reverse space-x-2">
                    <img src="{{asset('merikhArz/src/images/date.svg')}}" class="w-5 h-5" alt="">
                    <p class="text-mini-base">
                        تاریخ و زمان سفارش :
                    </p>
                    <div>
                        <p class="text-mini-base">
                            1403/09/10 22:00
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('merikhArz/src/images/seke.svg')}}" class="w-5 h-5" alt="">
                <p class="text-mini-base">
                    مبلغ:
                </p>
                <div>
                    <p class="text-mini-base">
                        1$
                        (81 هزار تومان)
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('merikhArz/src/images/perfectmone.svg')}}" class="w-5 h-5" alt="">
                <p class="text-mini-base">
                    کد رهگیری :
                </p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="text-mini-base">
                        200694787
                    </p>
                    <img src="{{asset('merikhArz/src/images/copy.svg')}}" class="w-5 h-5" alt="">
                </div>
            </div>

            <div class="flex items-center space-x-reverse space-x-2 btn-information cursor-pointer">
                <p class="text-mini-base underline underline-offset-2 decoration-1 decoration-sky-600 text-sky-600">
                    جزئیات بیشتر
                </p>

            </div>

            <div class="hidden flex-col justify-start space-y-4 information transition-all ">

                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="text-mini-base">
                        شماره سفارش :
                    </p>
                    <div>
                        <p class="text-mini-base">
                            1521
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="text-mini-base">
                        شماره حساب گیرنده:
                    </p>
                    <div>
                        <p class="text-mini-base">
                            U47590413
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="text-mini-base">
                        توضیحات:
                    </p>
                    <div>
                        <p class="text-mini-base">
                            انجام حواله پرفکت مانی با درگاه بانکی
                        </p>
                    </div>
                </div>
            </div>


        </article>

    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center space-x-2 space-x-reverse">
                <img src="{{asset('merikhArz/src/images/checked.svg')}}" alt="" class="w-6 h-6">
                <h1 class="text-sm">
                    افزایش مبلغ کیف پول
                </h1>
            </div>
            <img src="{{asset('merikhArz/src/images/IncreaseWallet.svg')}}" alt="">
        </header>
        <article class="flex flex-col justify-start space-y-4 p-2 ">
            <div>
                <div class="flex items-center space-x-reverse space-x-2">
                    <img src="{{asset('merikhArz/src/images/date.svg')}}" class="w-5 h-5" alt="">
                    <p class="text-mini-base">
                        تاریخ و زمان سفارش :
                    </p>
                    <div>
                        <p class="text-mini-base">
                            1403/09/10 22:00
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('merikhArz/src/images/seke.svg')}}" class="w-5 h-5" alt="">
                <p class="text-mini-base">
                    مبلغ:
                </p>
                <div>
                    <p class="text-mini-base">
                        1$
                        (81 هزار تومان)
                    </p>
                </div>
            </div>


            <div class="flex items-center space-x-reverse space-x-2">
                <p class="text-mini-base">
                    شماره سفارش :
                </p>
                <div>
                    <p class="text-mini-base">
                        1521
                    </p>
                </div>
            </div>

        </article>

    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center space-x-2 space-x-reverse">
                <img src="{{asset('merikhArz/src/images/checked.svg')}}" alt="" class="w-6 h-6">
                <h1 class="text-sm">
                    برداشت از کیف پول
                </h1>
            </div>
            <img src="{{asset('merikhArz/src/images/decreaseWallet.svg')}}" alt="">
        </header>
        <article class="flex flex-col justify-start space-y-4 p-2 ">
            <div>
                <div class="flex items-center space-x-reverse space-x-2">
                    <img src="{{asset('merikhArz/src/images/date.svg')}}" class="w-5 h-5" alt="">
                    <p class="text-mini-base">
                        تاریخ و زمان برداشت :
                    </p>
                    <div>
                        <p class="text-mini-base">
                            1403/09/10 22:00
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('merikhArz/src/images/seke.svg')}}" class="w-5 h-5" alt="">
                <p class="text-mini-base">
                    مبلغ:
                </p>
                <div>
                    <p class="text-mini-base">
                        1$
                        (81 هزار تومان)
                    </p>
                </div>
            </div>


            <div class="flex items-center space-x-reverse space-x-2">
                <p class="text-mini-base">
                    شماره سفارش :
                </p>
                <div>
                    <p class="text-mini-base">
                        1521
                    </p>
                </div>
            </div>

        </article>

    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center space-x-2 space-x-reverse">
                <img src="{{asset('merikhArz/src/images/closeRedIcon.svg')}}" alt="" class="w-6 h-6">
                <h1 class="text-sm">
                    حواله پرکت مانی
                </h1>
            </div>
            <img src="{{asset('merikhArz/src/images/perfectmone.svg')}}" alt="" class="w-6 h-6">
        </header>
        <article class="flex flex-col justify-start space-y-4 p-2 tra">
            <div>
                <div class="flex items-center space-x-reverse space-x-2">
                    <img src="{{asset('merikhArz/src/images/date.svg')}}" class="w-5 h-5" alt="">
                    <p class="text-mini-base">
                        تاریخ و زمان سفارش :
                    </p>
                    <div>
                        <p class="text-mini-base">
                            1403/09/10 22:00
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('merikhArz/src/images/seke.svg')}}" class="w-5 h-5" alt="">
                <p class="text-mini-base">
                    مبلغ:
                </p>
                <div>
                    <p class="text-mini-base">
                        1$
                        (81 هزار تومان)
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-reverse space-x-2">
                <p class="text-mini-base">
                    شماره سفارش :
                </p>
                <div>
                    <p class="text-mini-base">
                        1521
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-reverse space-x-2">
                <p class="text-mini-base">
                    توضیحات:
                </p>
                <div>
                    <p class="text-mini-base leading-4">
                        پرداخت موفقیت آمیز نبود انصراف کاربر از
                        درگاه پرداخت
                    </p>
                </div>
            </div>


        </article>

    </section>

@endsection

