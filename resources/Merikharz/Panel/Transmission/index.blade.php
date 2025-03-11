@extends('Panel.Layout.master')
@section('content')
    <section class="errors">

    </section>
    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">

            <img src="{{asset('merikhArz/src/images/perfectmone.svg')}}" alt="">
            <h1 class="text-sm font-bold">
                حواله پرفکت مانی
            </h1>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 ">
            <form action="{{route('panel.transmission')}}" class="space-y-4" id="form" method="post">
                @csrf
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-28 leading-4">آدرس حساب مقصد :</p>
                    <input type="text" class="py-1.5 px-4 border border-black rounded-md text-black w-full"
                           name="transmission">
                    <img src="{{asset('merikhArz/src/images/pasteIcon.svg')}}" alt="">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-28 leading-4">مبلغ حواله (دلار) :</p>
                    <input type="text"
                           class="customPayment py-1.5 px-4 border border-black rounded-md text-black w-full"
                           name="custom_payment">
                    <img src="{{asset('merikhArz/src/images/pasteIcon.svg')}}" alt="">
                </div>





                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class=" font-bold text-mini-mini-base w-24 leading-4 text-sky-600 underline underline-offset-8">
                        قوانین را می پذیرم :</p>
                    <input type="checkbox" class="py-1.5 px-4 border border-black rounded-md text-black "
                           name="Accepting_the_rules">
                </div>

                <div class="flex items-center rounded-lg space-x-2 space-x-reverse mt-4 ">
                    <img src="{{asset('merikhArz/src/images/seke.svg')}}" alt="">
                    <h1 class="text-sm">
                        <span class="text-mini-base font-bold text-rose-700 amountPayment">0</span> ریال
                    </h1>
                </div>

                <div class="flex items-center   rounded-lg space-x-2 space-x-reverse">
                    <img src="{{asset('merikhArz/src/images/seke.svg')}}" alt="">
                    <h1 class="text-sm">
                        موجودی فعلی شما <span
                            class="text-mini-mini-base font-bold   @if($user->getCreaditBalance()>0) text-green-600  @else text-rose-700 @endif">
                            {{numberFormat(empty(substr($user->getCreaditBalance(),0,(strlen($user->getCreaditBalance())-1)))?0: substr($user->getCreaditBalance(),0,(strlen($user->getCreaditBalance())-1)))}}
                        </span>
                        تومان میباشد
                    </h1>
                </div>

                @foreach($banks as $bank)

                    <div class="flex items-center justify-center action">
                        <button type="button" onclick="selectBank({{$bank->id}})"
                                class="bg-gradient-to-b from-268832 to-80C714 flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-2.5 text-white rounded-lg w-full">
                            <span class="w-48 text-mini-base leading-4">پرداخت با {{$bank->name??''}}</span>
                            <img src="{{asset('merikhArz/src/images/bankkart.svg')}}" alt="" class="bg-cover w-5 h-5 ">
                        </button>
                    </div>

                @endforeach
                <input type="hidden" value="" name="bank" id="bank" class="action">


                <div class="flex items-center justify-center action">
                    <button
                        class="bg-gradient-to-b from-DE9408 to-FFC98B flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-2.5 text-white rounded-lg w-full">
                        <span class="w-48 text-mini-base leading-4">پرداخت از طریق کیف پول</span>
                        <img src="{{asset('merikhArz/src/images/walletWhite.svg')}}" alt="" class="bg-cover w-5 h-5 ">
                    </button>
                </div>
                <div class="flex items-center justify-center  ">
                    <h1 class="font-bold text-xl flex items-center justify-center text-right loading">


                    </h1>
                </div>
            </form>


        </article>
    </section>

@endsection

@section('script')

    <script>
        var hiddenInputBank = document.getElementById('bank');
        var form = document.getElementById('form');


        function selectBank(value) {
            hiddenInputBank.value = value;
            form.action = "{{route('panel.transferFromThePaymentGateway')}}";
            form.submit();
          $(".action").remove();
          var html='<span style="--i:9;" class="animation flex items-center justify-center text-base-font-color">z</span>' +
              '<span style="--i:8;" class="animation flex items-center justify-center text-base-font-color">r</span>' +
              '<span style="--i:7;" class="animation flex items-center justify-center text-base-font-color">a</span>' +
              '<span style="--i:6;" class="animation flex items-center justify-center">h</span>' +
              '<span style="--i:5;" class="animation flex items-center justify-center">k</span>' +
              '<span style="--i:4;" class="animation flex items-center justify-center">i</span>' +
              '<span style="--i:3;" class="animation flex items-center justify-center">r</span>' +
              '<span style="--i:2;" class="animation flex items-center justify-center">e</span>' +
              '<span style="--i:1;" class="animation flex items-center justify-center">M</span>'
          $('.loading').append(html)
        }


        function commission() {
            return "{{$dollar->DollarRateWithAddedValue()}}";
        }


        $(".customPayment").on('input', function () {
            let payment = $('.customPayment').val();
            if (payment <= Number("{{env('Daily_Purchase_Limit')}}")) {
                let paymentResult = payment * commission();
                if (payment.match(/(([0-9])?((\.)?)([0-9]{1,2}))/gm)) {
                    if (payment.includes('.')) {
                        let paymentSplit = payment.split('.')[1]
                        if (paymentSplit.length > 1) {
                            var message = "شما تا یک رقم اعشار بیشتر نمیتوانید وارد کنید."
                            errors(message);
                            $('.customPayment').val('')
                        }
                    }

                    $('.amountPayment').text(' مبلغ قابل پرداخت: ' + formatNumber(paymentResult))

                } else {
                    var message = "مقدار ورودی باید از نوع عددی و کوچک تر از"
                    message += "{{env('Daily_Purchase_Limit')}}" + " .باشد ";
                    errors(message);
                    $('.customPayment').val('')
                }
            } else {
                var message = "مقدار ورودی باید از نوع عددی و کوچک تر از"
                message += "{{env('Daily_Purchase_Limit')}}" + " .باشد ";
                errors(message);
                $('.customPayment').val('')
            }
        });


        function formatNumber(number) {
            number = Math.floor(number / 10000) * 10000
            let string = number.toLocaleString('fa-IR'); // ۱۲٬۳۴۵٫۶۷۹
            number = string.replace(/\٬/g, ",‬");
            return number;
        }


        function errors(value) {
            let html = '<section class="container p-2 absolute space-y-2 max-w-max">' +
                '<div  class="toast transition-all duration-300 transform  flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">' +
                '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg dark:bg-orange-700 dark:text-orange-200">' +
                '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">' +
                '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>' +
                '</svg>' +
                '<span class="sr-only">Warning icon</span>' +
                '</div>' +
                '<div class="ms-3 text-sm font-normal">' + value + '</div>' +
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

        function close() {
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

@endsection
