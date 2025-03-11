@extends('Panel.Layout.master')
@section('content')
    <section class="errors">

    </section>
    @foreach($financeTransactions as $financeTransaction)

        <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
            <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <img src="
                      @if($financeTransaction->type!="bank")
                      {{asset('merikhArz/src/images/checked.svg')}}
                      @else
                        {{asset('merikhArz/src/images/closeRedIcon.svg')}}
                      @endif
                    " alt="" class="w-6 h-6">
                    <h1 class="text-sm">
                        @if($financeTransaction->type=="deposit")
                            افزایش مبلغ کیف پول
                        @elseif($financeTransaction->type=="bank")
                            خطا در انجام عملیات
                        @else
                            برداشت از کیف پول
                        @endif
                    </h1>
                </div>
                @if($financeTransaction->type=="deposit")
                    <img src="{{asset('merikhArz/src/images/IncreaseWallet.svg')}}" alt="">
                @elseif($financeTransaction->type=="withdrawal")
                    <img src="{{asset('merikhArz/src/images/decreaseWallet.svg')}}" alt="">
                @endif
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
                                {{\Morilog\Jalali\Jalalian::forge($financeTransaction->created_at)->format('Y/m/d H:i:s')}}
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
                            ({{numberFormat($financeTransaction->amount/10)}} هزار تومان)
                        </p>
                    </div>
                </div>


                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="text-mini-base">
                        شماره سفارش :
                    </p>
                    <div>
                        <p class="text-mini-base">
                            {{$financeTransaction->id}}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="text-mini-base">
                        توضیحات:
                    </p>
                    <div>
                        <p class="text-mini-base">
                            {{$financeTransaction->description??''}}
                        </p>
                    </div>
                </div>

            </article>

        </section>


                @if($financeTransaction->transmission)
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
                                            {{\Morilog\Jalali\Jalalian::forge($financeTransaction->created_at)->format('Y/m/d H:i:s')}}
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
                                        {{$financeTransaction->transmission->payment_amount}}
                                        ({{numberFormat($financeTransaction->amount/10)}} هزار تومان)
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
                                        {{$financeTransaction->transmission->payment_batch_num}}
                                    </p>
                                    <img src="{{asset('merikhArz/src/images/copy.svg')}}" class="w-5 h-5 cursor-pointer copy" alt="">
                                </div>
                            </div>



                            <div class="hidden flex-col justify-start space-y-4 information transition-all ">

                                <div class="flex items-center space-x-reverse space-x-2">
                                    <p class="text-mini-base">
                                        شماره سفارش :
                                    </p>
                                    <div>
                                        <p class="text-mini-base">
                                            {{$financeTransaction->id}}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-reverse space-x-2">
                                    <p class="text-mini-base">
                                        شماره حساب گیرنده:
                                    </p>
                                    <div>
                                        <p class="text-mini-base">
                                            {{$financeTransaction->transmission->payee_account}}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-reverse space-x-2">
                                    <p class="text-mini-base">
                                        توضیحات:
                                    </p>
                                    <div>
                                        <p class="text-mini-base">
                                            {{$financeTransaction->description??''}}
                                        </p>
                                    </div>
                                </div>
                            </div>


                        </article>

                    </section>


                @endif
    @endforeach




@endsection

@section('script')

    <script>
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
            let spanText = $(this).siblings('p').text();
            copyToClipboard(spanText);
        });
    </script>
@endsection
