@extends('Auth.Layout.master')

@section('header-content')
    <section class="errors">

    </section>
    <section class="h-14 flex items-center justify-center px-4">
        <div class="flex items-center space-x-reverse space-x-2">
            <p class="text-base font-black ">
                ورود به حساب کاربری
            </p>
        </div>

    </section>

@endsection
@section('content')

    <form id="form" class="flex flex-col justify-center items-center space-y-6 py-2  " method="POST" action="">
        @csrf
        <div class="flex items-center justify-between space-x-reverse space-x-2 w-full  ">
            <div class="flex items-center  border-2 px-2 h-12 rounded-md border-black">
                <img src="{{asset('merikhArz/src/images/svgPhone.svg')}}" alt="" class="w-6 h-6">
                <input type="text"
                       class=" mobile w-full h-full inline-block outline-none px-2 placeholder:text-center placeholder:text-sm"
                       placeholder="شماره همراه  (*********09)" name="mobile" >
            </div>
            <button class="text-mini-base bg-gradient-to-b from-80C714 to-268832 w-24   h-12 text-white rounded-md text-center flex items-center justify-center cursor-pointer send">
                ارسال
                کد
            </button>

        </div>
        <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
            <img src="{{asset('merikhArz/src/images/key.svg')}}" alt="" class="w-6 h-6">
            <input type="text"
                   class="w-full h-full py-1.5 outline-none px-2  placeholder:text-center  placeholder:text-sm"
                   placeholder="کد ارسال شده به تلفن همراه " name="code">
        </div>
        <div class="flex items-center space-x-reverse space-x-2 w-full border-black border-2 px-2 h-12 rounded-md">
            <img src="{{asset('merikhArz/src/images/key.svg')}}" alt="" class="w-6 h-6">
            <input type="password"
                   class="w-full h-full py-1.5 outline-none px-2  placeholder:text-center  placeholder:text-sm"
                   placeholder="کلمه عبور جدید (حروف و عدد)" name="password">
        </div>
        <div class="flex items-center justify-center text-mini-mini-base text-center leading-6 text-black/35 font-bold time w-full">
        </div>
        <button class="bg-gradient-to-b from-FFB01B to-DE9408 text-sm w-52 h-10 rounded-md text-white font-bold"
                type="">ورود به
            حساب کاربری
        </button>


    </form>
@endsection
@section('script-tag')

    <script>
        $(document).ready(function () {
            let sendBtn = $(".send");
            let tokenCSRF = "{{csrf_token()}}";
            $(sendBtn).click(function (event) {
                let mobile=$(".mobile").val();
                event.preventDefault();
                $.ajax({
                    'type': "POST",
                    'url': "{{route('createCode')}}",
                    data: {_token: tokenCSRF,mobile:mobile},
                    success: function (response) {
                        time(response.message);
                        $(sendBtn).attr("disabled","disabled");
                        $(sendBtn).removeClass('bg-gradient-to-b from-80C714 to-268832');
                        $(sendBtn).addClass('bg-gray-400');

                    },
                    error: function (error) {
                        let html = '<section class="container p-2 absolute space-y-2 max-w-max">' +
                            '<div  class="toast transition-all duration-300 transform  flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">' +
                            '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg dark:bg-orange-700 dark:text-orange-200">' +
                            '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">' +
                            '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>' +
                            '</svg>' +
                            '<span class="sr-only">Warning icon</span>' +
                            '</div>' +
                            '<div class="ms-3 text-sm font-normal">'+error.responseJSON.message+'</div>' +
                            '<button type="button" class="close-toast ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-warning" aria-label="Close">' +
                            '<span class="sr-only">Close</span>' +
                            '<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">' +
                            '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>' +
                            '</svg>' +
                            '</button>' +
                            ' </div>' +
                            '</section>'
                        $('.errors').append(html)
                        close();
                    }
                })
            })
        });
    </script>

    <script>
        function close()
        {
            $(document).ready(function () {
                let toast = $('.toast');
                setTimeout(function () {
                    $(toast).addClass('-translate-y-7');
                    $(toast).remove();
                }, 9000)


                let closeToast = $('.close-toast');
                $(closeToast).click(function () {
                    $(toast).addClass('invisible');
                    $(toast).remove();
                });
            })
        }
    </script>
    <script>
        function time(message){
           var sendBtn=document.getElementsByClassName("send")[0];

            var countDownDate = new Date(message.created_at)

            var now = new Date("{{\Carbon\Carbon::now()->subMinutes(3)->toDateTimeString()}}")


            // Update the count down every 1 second
            var x = setInterval(function () {
                now.setSeconds(now.getSeconds() + 1)


                // Get today's date and time

                // Find the distance between now and the count down date
                var distance = countDownDate - now;
                // Time calculations for days, hours, minutes and seconds

                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                // console.log(minutes)
                // Display the result in the element with id="demo"
                var text = '';
                if (minutes > 0) {
                    text += 'مدت زمان باقی مانده تا دریافت مجدد کد ' + minutes + ' دقیقه و ' + seconds + ' ثانیه  '
                } else {
                    text = 'مدت زمان باقی مانده تا دریافت مجدد کد ' + seconds + ' ثانیه  '
                }
                document.getElementsByClassName("time")[0].innerHTML = text

                // If the count down is finished, write some text
                if (distance < 0) {
                    document.getElementsByClassName("time")[0].innerHTML = ''

                    clearInterval(x);
                    sendBtn.removeAttribute('disabled')
                    sendBtn.classList.remove('bg-gray-400')
                    sendBtn.classList.add('bg-gradient-to-b','from-80C714','to-268832');

                }
                let form = document.getElementById('form');

                form.action = message.route
            }, 1000);
        }
    </script>
@endsection
