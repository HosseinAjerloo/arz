@extends('Panel.Layout.master')

@section('content')

    <section class="errors">

    </section>
    <section class="container mx-auto w-full md:w-1/2 lg:w-1/3 border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <div class="flex items-center px-.5 space-x-2 space-x-reverse ">
                @if($fastPayment->financeTransaction->payment->state!='finished')
                    <img src="{{asset("merikhArz/src/images/closeRedIcon.svg")}}" alt=""/>
                    <h1 class="text-sm font-bold">
                        خرید موفقیت آمیز نبود
                    </h1>
                @else
                    <img src="{{asset('merikhArz/src/images/checked.svg')}}" alt="" class="w-5 h-5">
                    <h1 class="text-sm font-bold">
                        خرید با موفقیت انجام شد
                    </h1>
                @endif

            </div>
            <div class="flex items-center space-x-reverse space-x-2">

                <p class="text-mini-base font-bold flex ">
                    <span style="--i:9;"
                          class="animation flex items-center justify-center text-base-font-color">z</span>
                    <span style="--i:8;"
                          class="animation flex items-center justify-center text-base-font-color">r</span>
                    <span style="--i:7;"
                          class="animation flex items-center justify-center text-base-font-color">a</span>
                    <span style="--i:6;" class="animation flex items-center justify-center">h</span>
                    <span style="--i:5;" class="animation flex items-center justify-center">k</span>
                    <span style="--i:4;" class="animation flex items-center justify-center">i</span>
                    <span style="--i:3;" class="animation flex items-center justify-center">r</span>
                    <span style="--i:2;" class="animation flex items-center justify-center">e</span>
                    <span style="--i:1;" class="animation flex items-center justify-center">M</span>
                </p>
                <img src="{{asset('merikhArz/src/images/utopia.png')}}" alt="" class="w-6 h-6">
            </div>

        </header>

        <article class="flex flex-col justify-center space-y-3 p-2 ">
            <div class="flex items-center ">
                <p class=" text-mini-base">
                    @if($fastPayment->financeTransaction->payment->state!='finished')
                        متاسفانه پرداخت شما با خطا مواجه شده است
                    @else

                        پرداخت موفقیت آمیز بود شما میتوانید از قسمت سوابق تراکنش های خود را مشاهده کنید
                    @endif
                </p>
            </div>

            @if($fastPayment->financeTransaction->payment->state=='finished')

                <div class="flex items-center ">
                    <div class="flex items-center space-x-reverse space-x-2">
                        <p class="flex items-center justify-center text-mini-base">
                            مبلغ پرداخت :{{$fastPayment->amount??''}} دلار
                        </p>
                    </div>
                </div>
            @endif
            @if($fastPayment->financeTransaction->payment->state!='finished')

                <div class="flex items-center ">
                    <div class="flex items-center space-x-reverse ">
                        <p class="flex items-center justify-center text-mini-base">
                            در صورت کم شدن مبلغ به کیف پول شما اضافه خواهد شد.
                        </p>
                    </div>
                </div>
            @endif
        </article>

    </section>

@endsection

@section('script-tag')

    <script>
        $(".share").click(function () {
            let body = $("body").html();
            let htmlPrint = $('.print').html();
            $("body").html(htmlPrint);
            window.print()
            $("body").html(body);

        })

        function copyToClipboard(text) {

            var textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy', err);
            }
            document.body.removeChild(textArea);
        }

        $('.copy').click(function () {

            let spanText = $(this).siblings('span').text();
            copyToClipboard(spanText);
        });

        {{--setTimeout(function (){--}}
        {{--    --}}{{--window.location.replace("{{$fastPayment->url_back?$fastPayment->url_back:route('panel.index')}}");--}}

        {{--},2000)--}}

    </script>
@endsection

