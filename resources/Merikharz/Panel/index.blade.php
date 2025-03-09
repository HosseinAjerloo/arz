@extends('Panel.Layout.master')
@section('content')
<section class="w-full relative flex items-center justify-center">
    <img src="{{asset('merikhArz/src/images/mainImg.png')}}" alt="" class="object-cover bg-center h-full w-full">
    <div class="absolute bottom-4 text-center py-3  z-[9] w-80 bg-black/45 border border-white/15 rounded-md
        text-white text-lg font-black tracking-wide">
        مریخ <spna class="text-base-font-color">ارز</spna>: معامله آسان رمزارزها
    </div>
</section>
<section class="w-full flex flex-wrap justify-between mt-4">
    <a href="{{route('panel.transmission.view')}}" class="w-[48%] h-40 bg-gradient-to-b from-FFC98B to-FFF5EA rounded-md flex justify-center items-center flex-col mt-2">
        <img src="{{asset("merikhArz/src/images/perfectMoneyIcon.png")}}" alt="" class="object-cover">
        <p class="text-base">حواله پرفکت مانی</p>
    </a>
    <div class="w-[48%] h-40 bg-gradient-to-b from-FFBEBE to-FFF5EA rounded-md flex justify-center items-center flex-col mt-2">
        <img src="{{asset('merikhArz/src/images/server.png')}}" alt="" class="object-cover">
        <p class="text-base">خرید سرور اختصاصی</p>
    </div>
    <div class="w-[48%] h-40 bg-gradient-to-b from-8EBFFC to-E5F1FF rounded-md flex justify-center items-center flex-col mt-2">
        <img src="{{asset('merikhArz/src/images/host.png')}}" alt="" class="object-cover">
        <p class="text-base text-center">خرید از سایت های خارجی</p>
    </div>
    <a href="{{route('panel.wallet.charging')}}" class="w-[48%] h-40 bg-gradient-to-b from-DBBBFF to-F6EDFF rounded-md flex justify-center items-center flex-col mt-2">
        <img src="{{asset('merikhArz/src/images/host.png')}}" alt="" class="object-cover">
        <p class="text-base text-center">شارژ حساب شما</p>
    </a>

</section>

<section class="p-10 flex px-3 space-x-reverse space-x-3">
    <div>
        <img src="{{asset("merikhArz/src/images/checked.png")}}" alt="" class="object-cover">
    </div>
    <div>
        <h2 class="font-semibold  text-base">
            انجام امور ارزی شما در کمترین زمان
        </h2>
        <p class="mt-2.5 text-mini-base">
            پرداخت های ایمن و مطمئن با مریخ ارز
        </p>
    </div>
</section>

@guest
    <section
        class="mt-2 bg-no-repeat w-[100%] bg-cover	bg-center  sm:bg-[position:unset]   h-80 relative flex items-center flex-col"
        style="background-image: url('{{ asset('merikhArz/src/images/bg-fastLogin.png') }}')">
        <span class="font-black text-lg border border-base-font-color rounded-2xl px-4 py-1.5 max-h-min bg-F4F7FB ">
            ثبت نام آسان !
        </span>
        <form action="" class="mt-10 space-y-8">
            <div class="flex bg-white w-full border rounded-md p-2 ">
                <img src="{{asset('merikhArz/src/images/svgPhone.svg')}}" alt="" class="ml-2">
                <input type="text" placeholder="شماره تلفن خود را وارد کنید!" class="placeholder:text-mini-base placeholder:text-black px-2 bg-transparent
                outline-none">
            </div>

            <div class=" text-center">
                <p class="text-mini-base">
                    اگر قبلا ثبت نام کرده اید، <a href="" class="text-sky-600"> وارد شوید!</a>
                </p>
            </div>

            <div class="text-center w-full ">
                <button class="text-md  rounded-2xl px-12 py-1.5  bg-gradient-to-b from-80C714 to-268832 text-white">ثبت
                    نام
                </button>

            </div>
        </form>
    </section>
@endguest

<section class="bg-F5F5F5 py-1.5 px-4 rounded-md mt-6">
    <h1 class="text-mini-base text-center">
        <span class="font-bold text-lg">مریخ</span> <span class="text-base-font-color font-bold text-lg"> ارز</span>،
        هر آنچه برای معاملات ارزی خود نیاز دارید
    </h1>
    <ul class="space-y-3 mt-4">
        <li class="text-mini-base leading-5">
            تبادل کلیه ارزهای دیجیتال و پول های الکترونیک با حداقل کارمزد
        </li>
        <li class="text-mini-base leading-5">
            خرید سرورهای اختصاصی از کشورهای مختلف با قیمت استثنایی
        </li>
        <li class="text-mini-base leading-5">
            خرید آسان و مطمئن از سایت های متنوع خارجی
        </li>
        <li class="text-mini-base leading-5">
            شارژ حساب های خارجی و کیف پول شما
        </li>
        <li class="text-mini-base leading-5">
            شارژ حساب های خارجی و ولت شما
        </li>
        <li class="text-mini-base leading-5">
            پشتیبانی و پاسخگویی 24 ساعته
        </li>
    </ul>
</section>
@endsection
