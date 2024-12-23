<header class="w-full h-16 ">
    <nav
        class="px-4 h-full bg-base-bg-color  flex items-center justify-between border-b-base-font-color border-b relative">
        <!--  user Guest   -->
        @guest
            <div class="flex space-x-reverse space-x-3">
                <img src="{{asset('merikhArz/src/images/hamburger.svg')}}" alt="" class="btnMenu">
                <div class="flex space-x-reverse space-x-2">
                    <h1 class="font-bold text-xl">
                        مریخ
                        <span class="text-base-font-color">ارز</span>
                    </h1>
                    <img src="{{asset("merikhArz/src/images/merikhIcon.svg")}}" alt="">
                </div>
            </div>
        @endguest

        <!-- end  user Guest   -->

        <!--  user has login   -->

        @auth

            <div class="flex space-x-reverse space-x-3">
                <img src="{{asset("merikhArz/src/images/hamburger.svg")}}" alt="" class="btnMenu cursor-pointer">
                <div class="relative">
                    <img src="{{asset("merikhArz/src/images/userIcon.svg")}}" alt="" class="userIcon ">
                    <div
                        class="profile cursor-pointer -z-10 absolute w-60 top-12 right-0 bg-white p-2  rounded-ee-2xl transition-all  translate-y-3 opacity-0">
                        <ul class="w-full">
                            <li class="text-sm h-8 border-b-base-font-color border-dashed border-b text-black/75 py-1.5 flex items-center space-x-reverse space-x-2">
                                <img src="{{asset('merikhArz/src/images/userIcon.svg')}}" alt="" class="w-4 h-4">

                                <p>

                                    {{\Illuminate\Support\Facades\Auth::user()->fullName}} ({{\Illuminate\Support\Facades\Auth::user()->mobile}})
                                </p>
                            </li>
                            <li class="text-sm h-8 border-b-base-font-color border-dashed border-b text-black/75 py-1.5 flex items-center space-x-reverse space-x-2">
                                <img src="{{asset('MerikhArz/src/images/pen.svg')}}" alt="" class="w-5 h-5">

                                <p>
                                    ویرایش پروفایل
                                </p>
                            </li>
                            <li class="text-sm h-8  text-black/45 px-1.5 py-6 flex items-center">
                                <div
                                    class=" text-mini-base border border-base-font-color rounded-2xl px-6 py-2 max-h-min bg-F4F7FB flex items-center justify-center mr-2">
                                    <img src="{{asset("merikhArz/src/images/logout.svg")}}" alt="" class="ml-2 w-5 h-5">
                                    <a href="{{route('logout')}}"> خروج از حساب</a>
                                </div>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>


            <div class="flex space-x-reverse space-x-2">
                <h1 class="font-bold text-xl">
                    مریخ
                    <span class="text-base-font-color">ارز</span>
                </h1>
                <img src="{{asset('merikhArz/src/images/merikhIcon.svg')}}" alt="">
            </div>
        @endguest
        <!-- end user has login   -->

        <!--  user Guest   -->
        @guest
            <div
                class="flex text-base bg-gradient-to-r from-FFB01B to-DE9408 py-1.5 px-2.5 rounded-md text-white shadow-sm shadow-inner">
                <a href="{{route('login.index')}}">ورود / ثبت نام</a>
            </div>
        @endguest
        <!-- end  user Guest   -->

        <!--  user has login   -->
        @auth
            <div class="flex text-base ">
                <img src="{{asset("merikhArz/src/images/notification.svg")}}" alt="">
            </div>
        @endauth
        <!-- end user has login   -->
        <div
            class="menuItem invisible absolute w-80 top-16 right-[100%] bg-white p-2 z-10 rounded-ee-2xl transition-all">
            <ul class="w-full">
                <li class="text-sm h-8 border-b-base-font-color border-dashed border-b text-black p-1.5 ">
                    <a href="">
                        سفارشات شما
                    </a>
                </li>
                <li class="text-sm h-8 border-b-base-font-color   border-dashed  border-b text-black p-1.5">
                    <a href="">
                        حواله پرفکت مانی
                    </a>
                </li>
                <li class="text-sm h-8 border-b-base-font-color   border-dashed border-b text-black p-1.5">
                    <a href="">
                        شارژ حساب های شما
                    </a>
                </li>
                <li class="text-sm h-8 border-b-base-font-color  border-dashed border-b text-black p-1.5">
                    <a href="">
                        خرید سرور اختصاصی
                    </a>
                </li>
                <li class="text-sm h-8 border-b-base-font-color  border-dashed border-b text-black p-1.5">
                    <a href="">
                        خرید از سایت های خارجی
                    </a>
                </li>
                <li class="text-sm h-8  text-black p-1.5">
                    <a href="">
                        تیکت به پشتیبانی
                    </a>
                </li>
                <li class="text-sm h-8  text-black/45 px-1.5 py-6 flex items-center">
                    <div
                        class=" text-mini-base border border-base-font-color rounded-2xl px-6 py-2 max-h-min bg-F4F7FB flex items-center justify-center">
                        <img src="{{asset("merikhArz/src/images/call.svg")}}" alt="" class="ml-2 w-4 h-4">
                        <a href="">تماس باما</a>
                    </div>
                    <div
                        class=" text-mini-base border border-base-font-color rounded-2xl px-6 py-2 max-h-min bg-F4F7FB flex items-center justify-center mr-2">
                        <img src="{{asset("merikhArz/src/images/desc.svg")}}" alt="" class="ml-2 w-4 h-4">
                        <a href="">درباره ما</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
