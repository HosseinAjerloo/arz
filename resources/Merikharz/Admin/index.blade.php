@extends('Panel.Layout.master')
@section('content')
    <section class="errorsAlert z-[1000]">


    </section>
    <section class="w-full relative flex items-center justify-center">
        <img src="{{asset('merikhArz/src/images/mainImg.png')}}" alt="" class="object-cover bg-center h-full w-full 2xl:w-1/2">
        <div class="absolute bottom-4 text-center py-3  z-[9] w-80 bg-black/45 border border-white/15 rounded-md
        text-white text-lg font-black tracking-wide">
            مریخ
            <spna class="text-base-font-color">ارز</spna>
            : معامله آسان رمزارزها
        </div>
    </section>
    <section class="w-full flex flex-wrap justify-between mt-4">
        <a href="{{route('panel.transmission.view')}}"
           class="w-[48%] h-40 bg-gradient-to-b from-FFC98B to-FFF5EA rounded-md flex justify-center items-center flex-col mt-2">
            <img src="{{asset("merikhArz/src/images/adminTicket.svg")}}" alt="" class="object-cover w-28 h-28">
            <p class="text-base mt-6">تیکت های پیشتیبانی</p>
        </a>
        <div
            class="w-[48%] h-40 bg-gradient-to-b from-FFBEBE to-FFF5EA rounded-md flex justify-center items-center flex-col mt-2">
            <img src="{{asset('merikhArz/src/images/dollarAdmin.svg')}}" alt="" class="object-cover w-28 h-28">
            <p class="text-base  mt-6">تنظیم نرخ دلار</p>
        </div>

        <a href="{{route('panel.wallet.charging')}}"
           class="w-full h-40 bg-gradient-to-b from-DBBBFF to-F6EDFF rounded-md flex justify-center items-center flex-col mt-2">
            <img src="{{asset('merikhArz/src/images/usersAdmin.svg')}}" alt="" class="object-cover w-28 h-28">
            <p class="text-base text-center mt-6">کاربران</p>
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

@endsection
@section('script')

    <script>
        $(document).ready(function () {
            let sendBtn = $(".send");
            let registerBtn = $(".register");
            let tokenCSRF = "{{csrf_token()}}";
            sendBtn.click(function (event) {


                let mobile = $(".mobile").val();

                event.preventDefault();
                $.ajax({
                    'type': "POST",
                    'url': "{{route('createCode')}}",
                    data: {_token: tokenCSRF, mobile: mobile},
                    success: function (response) {
                        time(response.message);
                        $(sendBtn).addClass('hidden');

                        let setPassword=document.getElementsByClassName('password');
                        for(const div of setPassword)
                        {
                            div.classList.remove('inFade')
                        }
                        $(registerBtn).removeClass('hidden')

                    },
                    error: function (error) {
                        showError(error)
                    }
                })
            })
        });

        function showError(error) {
            let html = `<section class="container p-2">

                    <div id="toast-danger" class="toast flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
                        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                            </svg>
                            <span class="sr-only">Error icon</span>
                        </div>
                        <div class="ms-3 text-sm font-normal">${error.responseJSON.message}</div>
                        <button type="button" class="close-toast ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-danger" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                    </section>
                    `;
            $('.errorsAlert').append(html);
            $('.errorsAlert').scrollIntoView()
            close();
        }
    </script>

    <script>
        function time(message) {
            var sendBtn = document.getElementsByClassName("send")[0];
            var registerBtn = document.getElementsByClassName("register")[0];

            var countDownDate = new Date(message.created_at)

            var now = new Date("{{\Carbon\Carbon::now()->subMinutes(3)->toDateTimeString()}}")
            let form = document.getElementById('form');


            var x = setInterval(function () {
                now.setSeconds(now.getSeconds() + 1)


                var distance = countDownDate - now;


                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                var text = '';
                if (minutes > 0) {
                    text += 'مدت زمان باقی مانده تا دریافت مجدد کد ' + minutes + ' دقیقه و ' + seconds + ' ثانیه  '
                } else {
                    text = 'مدت زمان باقی مانده تا دریافت مجدد کد ' + seconds + ' ثانیه  '
                }
                document.getElementsByClassName("time")[0].innerHTML = text

                if (distance < 0) {
                    document.getElementsByClassName("time")[0].innerHTML = ''

                    clearInterval(x);
                    sendBtn.removeAttribute('disabled')
                    sendBtn.classList.remove('bg-gray-400')
                    sendBtn.classList.add('bg-gradient-to-b', 'from-80C714', 'to-268832');
                    sendBtn.classList.toggle('send');
                    sendBtn.textContent='دریافت مجددا رمز یک بار مصرف'
                    sendBtn.classList.remove('hidden')
                    registerBtn.classList.add('hidden')
                    let setPassword=document.getElementsByClassName('password');
                    for(const div of setPassword)
                    {
                        div.classList.remove('inFade')
                    }
                    form.action = '';
                    form.removeAttr('method')

                }

                form.action = message.route
                form.method='POST'
            }, 1000);
        }

    </script>

@endsection
