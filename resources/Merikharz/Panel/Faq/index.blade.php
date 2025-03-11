@extends('Panel.Layout.master')
@section('header-content')
    <section class="py-3 flex items-center justify-center space-x-reverse space-x-2">
        <a href="{{route('panel.ticket-add')}}" class="text-sm  flex items-center justify-center space-x-reverse space-x-2 bg-gradient-to-b from-268832 to-80C714 px-6 py-2 rounded-md text-white">
            <img src="{{asset('merikhArz/src/images/poshtibani.svg')}}" alt="">
            <span>تیکت به پشتیبانی</span>
        </a>
    </section>
    <section class="bg-DFEDFF h-14 flex items-center justify-center space-x-reverse space-x-2">
        <img src="{{asset('merikhArz/src/images/question.svg')}}" alt="" class="w-8 h-8">
        <p class="text-sm font-bold ">
            سوالات متداول
        </p>
    </section>
@endsection
@section('content')

    <section class=" w-full border-2 border-black/50 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center ">
                <h1 class="text-mini-base font-bold">
                    1- علت برگشت خوردن پول و انجام نشدن تراکنش چیست؟
                </h1>
            </div>
        </header>
        <article class="flex flex-col justify-start space-y-4 p-2 ">

            <p class="text-sm leading-6 text-right">
                پس از اینکه شما پرداخت آنلاین را در درگاه انجام دادید باید صبر کنید تا به سایت ما (مریخ ارز) باز گردید،
                در صورتی که در صفحه مرورگر دکمه بازگشت یا رفرش رو بزنید پرداخت ناموفق شده و پول ظرف مدت 72 ساعت به
                حسابتان بر میگردد.

        </article>

    </section>
    <section class=" w-full border-2 border-black/50 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center ">
                <h1 class="text-mini-base font-bold">
                    2- آیا انجام احراز هویت در مریخ <span class="text-base-font-color">ارز</span> الزامی هست؟
                </h1>
            </div>
        </header>
        <article class="flex flex-col justify-start space-y-4 p-2 ">

            <p class="text-sm leading-6 text-right">

                طبق دستورالعمل بانک مرکزی و برای جلوگیری از سو استفاده و پولشویی اطلاعات کارت بانکی ، موبایل و کد ملی
                شما بایستی در سایت ما ثبت شده باشد.مریخ ارز این اطمینان را به شما میدهد که کلیه اطلاعات اعلامی شما امن و
                محرمانه نگهداری خواهد شد.
            </p>

        </article>

    </section>
    <section class=" w-full border-2 border-black/50 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center ">
                <h1 class="text-mini-base font-bold">
                    3- سفارشات انجام شده را از کدام قسمت ببینم؟
                </h1>
            </div>
        </header>
        <article class="flex flex-col justify-start space-y-4 p-2 ">

            <p class="text-sm leading-6 text-right">

                3- سفارشات انجام شده را از کدام قسمت ببینم؟
                شما می توانید در صفحه اصلی پنل کاربری سه سفارش آخر را مشاهده فرمائید. در صورتی که نیاز به دیدن کلیه سفارش ها دارید روی دکمه
                <a href="{{route('panel.order')}}" class="text-sm mr-2 ml-2 mt-2 py-2 bg-gradient-to-b from-DE9408 to-FFB01B px-25-100 py-20-100 rounded-md text-white">
                    لیست کامل سفارشات
                </a>
                کلیک نمائید.
            </p>

        </article>

    </section>
@endsection
