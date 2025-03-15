@extends('Panel.Layout.master')
@section('header-content')
    <section class="bg-DFEDFF h-14 flex items-center justify-between px-4">
        <div class="flex items-center space-x-reverse space-x-2">
            <img src="{{asset('merikhArz/src/images/utopia.png')}}" alt="" class="w-6 h-6">
            <p class="text-mini-base font-bold ">
                ایجاد ووچر یوتوپیا
            </p>
        </div>
        <div class="flex items-center space-x-reverse space-x-2">

            <p class="text-mini-base font-bold flex ">
                <span style="--i:9;" class="animation flex items-center justify-center text-base-font-color">z</span>
                <span style="--i:8;" class="animation flex items-center justify-center text-base-font-color">r</span>
                <span style="--i:7;" class="animation flex items-center justify-center text-base-font-color">a</span>
                <span style="--i:6;" class="animation flex items-center justify-center">h</span>
                <span style="--i:5;" class="animation flex items-center justify-center">k</span>
                <span style="--i:4;" class="animation flex items-center justify-center">i</span>
                <span style="--i:3;" class="animation flex items-center justify-center">r</span>
                <span style="--i:2;" class="animation flex items-center justify-center">e</span>
                <span style="--i:1;" class="animation flex items-center justify-center">M</span>
            </p>
            <img src="{{asset('merikhArz/src/images/utopia.png')}}" alt="" class="w-6 h-6">
        </div>


    </section>
@endsection
@section('content')
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4 ">
        <header class="flex items-center justify-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/checked.svg')}}" alt="" class="w-5 h-5 hidden image-success">
            <i class="fa-solid fa-spinner loading text-green-600"></i>
            <h1 class="text-smfont-bold message">
                درحال ساخت ووچر یوتوپیا
            </h1>
        </header>
        <article class="flex flex-col justify-center space-y-3 p-2 hidden voucher-status">
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">کد رهگیری :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <img src="{{asset('merikhArz/src/images/copy.svg')}}" alt="" class="w-4 h-4 copy cursor-pointer">
                    <p class="flex items-center justify-center text-mini-base voucher-code">
{{--                        {{$voucher->code??''}}--}}
                        USD-1YVV-FLHf-qOyF-ampk-JDDt
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">تاریخ :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
{{--                        {{\Illuminate\Support\Carbon::make($voucher->created_at)->format('H:i:s Y/m/d')}}--}}
                        1403/01/29 10:11:53
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">شماره خرید :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
{{--                        {{$voucher->financeTransaction->id??''}}--}}
                        12
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">مبلغ حواله :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base voucher-amount ">
{{--                        {{$payment_amount??''}}--}}
                        1.2
                    </p>
                </div>
            </div>

        </article>
        <div class="flex justify-center items-center py-3 space-x-2 space-x-reverse">
            <a href="{{route('panel.index')}}" class="bg-gradient-to-b from-DE9408 to-FFB01B flex items-center text-mini-mini-base px-4 py-2.5 text-white rounded-lg">
                بازگشت به پنل کاربری
            </a>
            <button disabled onclick="redirect()" class="again  bg-gradient-to-b from-80C714 to-268832 flex items-center text-mini-mini-base px-4 py-2.5 text-white rounded-lg ">
                درخواست مجددا
            </button>
        </div>

    </section>


@endsection
@section('script')

    <script>

        function copyToClipboard(text) {

            var textArea = document.createElement( "textarea" );
            textArea.value = text;
            document.body.appendChild( textArea );
            textArea.select();

            try {
                var successful = document.execCommand( 'copy' );
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy',err);
            }
            document.body.removeChild( textArea );
        }

        $( '.copy' ).click( function()
        {
            let spanText=$(this).siblings('p').text();
            copyToClipboard( spanText);
        });
    </script>
    <script>
        var createVoucher=function (){
            var dataValue={
                _token:"{{csrf_token()}}",
            }
            $.ajax({
                method:'POST',
                url: "{{route('panel.deliveryVoucherBank',[$invoice, $payment])}}",
                data:dataValue,
                timeout:20000,
                success: function(response){
                    if(response.status!='undefined' && response.status)
                    {
                        $("#voucher-status").removeClass('hidden');
                        $("#voucher-code").html(response.voucher.code)
                        $("#voucher-amount").html(response.payment_amount)
                        $(".loading").addClass('hidden')
                        $("#message").html('کارت هدیه یوتوپیا بامشخصات ذیل برای شما ایجاد شد')
                        $(".image-success").removeClass('hidden');
                    }
                    else {
                        $("#message").html('روند ایجاد کارت هدیه یوتوپیا با مشکل روبه رو شد')
                        $(".loading").addClass('text-rouse-500')
                        again();
                    }
                },
                error: function(error){
                    $("#message").html('روند ایجاد کارت هدیه یوتوپیا با مشکل روبه رو شد')
                    $(".loading").addClass('text-rouse-500')
                    again();
                },
            });
        }();


        var countDownDate = new Date("{{\Carbon\Carbon::now()->toDateTimeString()}}")

        var now = new Date("{{\Carbon\Carbon::now()->subMinutes(2)->toDateTimeString()}}")


        // Update the count down every 1 second
        function again()
        {
            var x = setInterval(function () {
                now.setSeconds(now.getSeconds() + 1)


                // Get today's date and time

                // Find the distance between now and the count down date
                var distance = countDownDate - now;
                console.log(distance)
                // Time calculations for days, hours, minutes and seconds

                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                // console.log(minutes)
                // Display the result in the element with id="demo"
                var text = '';
                if (minutes > 0) {
                    text += 'مدت زمان باقی مانده تا تلاش مجدد ' + minutes + ' دقیقه و ' + seconds + ' ثانیه  '
                } else {
                    text = 'مدت زمان باقی مانده تا تلاش مجدد ' + seconds + ' ثانیه  '
                }
                document.querySelector(".again").innerHTML = text

                // If the count down is finished, write some text
                if (distance < 0) {
                    clearInterval(x);
                    let resend_btn = document.querySelector(".again")
                    resend_btn.innerHTML = "ارسال درخواست مجددا";
                    resend_btn.removeAttribute('disabled')
                }
            }, 1000);
        }
    </script>
    <script>
        function redirect()
        {
            window.location.href="{{route('panel.deliveryVoucherBankView',[$invoice, $payment])}}"
        }
    </script>
@endsection
