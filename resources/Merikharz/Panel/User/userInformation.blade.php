@extends('Panel.Layout.master')

@section('content')

    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/starSvg.svg')}}" alt="">
            <h1 class="text-sm">
                سرویس های پرطرفدار
            </h1>
        </header>
        <article class="flex items-center justify-between p-2 space-x-2 space-x-reverse">
            <img src="{{asset('merikhArz/src/images/arrowRight.svg')}}" alt="" class="bg-cover w-8 h-8 arrowRight">
            <ul class=" w-[90%] h-20 flex overflow-hidden slider relative">
                <li class="w-full h-full bg-gradient-to-b from-FFC98B to-FFC98B flex items-center rounded-lg absolute active transition-all">
                    <img src="{{asset('merikhArz/src/images/perfectMoneyIcon.png')}}" alt="" class="w-16 h-16 bg-cover mr-4">
                    <p class="text-sm mr-2">
                        حواله پرفکت مانی
                    </p>
                </li>
                <li class="w-full h-full bg-gradient-to-b from-CCCCCC to-EEEEEE flex items-center rounded-lg  translate-x-full absolute transition-all">
                    <img src="{{asset('merikhArz/src/images/server.png')}}" alt="" class="w-16 h-16 bg-cover mr-4">
                    <p class="text-sm mr-2">
                        سرور اختصاصی <span class="text-mini-mini-base font-bold text-red-700">(به زودی)</span>
                    </p>
                </li>
                <li class="w-full h-full bg-gradient-to-b from-F6EDFF to-DBBBFF flex items-center rounded-lg  translate-x-full transition-all">
                    <img src="{{asset('merikhArz/src/images/host.png')}}" alt="" class="w-16 h-16 bg-cover mr-4">
                    <p class="text-sm mr-2">
                        هاست خارجی <span class="text-mini-mini-base font-bold text-red-700">(به زودی)</span>
                    </p>
                </li>

            </ul>
            <img src="{{asset('merikhArz/src/images/arrowLeft.svg')}}" alt="" class="bg-cover w-8 h-8 arrowLeft">
        </article>

    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/windowsIcon.svg')}}" alt="">
            <h1 class="text-sm">
                پنل کاربری شما
            </h1>
        </header>
        <article class="flex flex-col justify-start space-y-2.5 p-2 ">
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('merikhArz/src/images/circleUser.svg')}}" alt="" class="w-6 h-6">
                <p class="text-sm">
                    {{$user->fullName??''}} ({{$user->mobile??''}})
                </p>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('merikhArz/src/images/plus.svg')}}" alt="" class="w-6 h-6">
                <p class="text-sm">
                    موجودی کیف پول شما <span class="font-bold text-mini-base @if($user->getCreaditBalance()>0) text-green-600 @else text-rose-700 @endif ">{{numberFormat($user->getCreaditBalance()/10)}}</span> میباشد.
                </p>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">
                <img src="{{asset('MerikhArz/src/images/pen.svg')}}" alt="" class="w-6 h-6">
                <a href="{{route('panel.user.edit')}}" class="text-sm">ویرایش حساب کاربری</a>
            </div>
        </article>
    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center space-x-reverse space-x-1">
                <img src="{{asset('merikhArz/src/images/RotationArrow.svg')}}" alt="">
                <h1 class="text-sm">
                    آخرین سفارشات
                </h1>
            </div>
            <div class="flex text-mini-base bg-gradient-to-r from-FFB01B to-DE9408 py-2 px-2.5 rounded-md text-white shadow-sm shadow-inner">
                <a href="{{route('panel.order')}}" class="text-mini-base">لیست کامل سفارشات</a>
            </div>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 ">
            <ul class="flex items-center justify-between pb-2 border-b-2 border-b-black">
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">
                        شماره سفارش
                    </p>
                </li>
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">
                        تاریخ
                    </p>
                </li>
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">مبلغ</p>
                    <p class="leading-4">(هزارتومان)</p>
                </li>
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">وضعیت</p>
                </li>
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">
                        توضیحات
                    </p>
                </li>
            </ul>
        </article>
        @foreach($financeTransactions as $financeTransaction)
            <article class="flex flex-col justify-start space-y-2 p-2 ">
                <ul class="flex items-center justify-between pb-2 border-b-2 border-b-black">
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <p class="font-medium leading-4">
                            {{$financeTransaction->id}}
                        </p>
                    </li>
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <p class="font-medium leading-4">
                            {{\Morilog\Jalali\Jalalian::forge($financeTransaction->created_at)->format('H:i:s Y/m/d')}}
                        </p>
                    </li>
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <p class="font-medium leading-4">{{numberFormat($financeTransaction->amount/10)}}</p>
                    </li>
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <p class="font-medium leading-4 text-center flex items-center justify-center">
                            <img class="w-5" src="  @if($financeTransaction->type!="bank"){{asset('merikhArz/src/images/checked.svg')}}@else{{asset('merikhArz/src/images/closeRedIcon.svg')}}@endif" alt="">
                        </p>
                    </li>
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <p class="font-medium leading-4 text-center flex items-center justify-center space-x-reverse space-x-1">
                            <span>{{$financeTransaction->description??''}}</span>
                        </p>
                    </li>

                </ul>
            </article>
        @endforeach


    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/warning.svg')}}" alt="">
            <h1 class="text-sm">
                اخبار و اطلاعیه ها
            </h1>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 ">
            <div class="flex items-center space-x-reverse space-x-2">
                <p class="text-mini-base font-bold">
                    پرداخت با حواله پرفکت مانی
                </p>
            </div>
            <div class="space-x-reverse space-x-2">
                <p class=" text-mini-mini-base leading-4 notice-desc">
                    کاربران گرامی توجه فرمائید پرداخت با حواله پرفکت مانی به مشکل برخورده است روش جایگزین به زودی
                    کاربران گرامی توجه فرمائید پرداخت با حواله پرفکت مانی به مشکل برخورده است روش جایگزین به زودی
                    کاربران گرامی توجه فرمائید پرداخت با حواله پرفکت مانی به مشکل برخورده است روش جایگزین به زودی
                </p>
                <span class="text-sky-600 text-mini-base notice  cursor-pointer"></span>
            </div>
        </article>
    </section>
    <section class=" w-full flex  justify-center mt-4 py-2">
        <a href="{{route('panel.faq')}}" class="bg-gradient-to-b from-268832 to-80C714 flex items-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-1.5 text-white rounded-lg">
            <img src="{{asset('merikhArz/src/images/ticket.svg')}}" alt="" class="bg-cover w-5 h-5 ">
            تیکت به پشتیبانی

        </a>
    </section>

@endsection
