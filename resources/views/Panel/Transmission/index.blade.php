@extends('Panel.layout.master')

@section('message-box')
    <div class="flex items-center justify-center flex-col space-y-3">
        <p class="text-center text-sm py-2 sm:text-base text-center">
            مبلغ ووچر پرفکت مانی درخواستی را وارد کنید یا از انتخاب سریع استفاده نمائید.
        </p>
            <article class="space-y-9  flex flex-col items-center justify-center w-full ">
                <div class="sm:space-x-3 sm:space-x-reverse sm:space-y-0 space-y-3  w-full flex flex-col sm:flex-row  text-center items-center">
                    <label for="" class="text-sm sm:text-base font-semibold inline-block w-2/4 ">آدرس حساب مقصد</label>
                    <input type="text"
                           class="rounded-md bg-gray-100 py-1 ring-8 ring-gray-300 ring-opacity-25 outline-none  transmission text-black w-3/4 inline-block" value="{{old('transmission')}}">
                </div>
                <div class="sm:space-x-3 sm:space-x-reverse sm:space-y-0 space-y-3 w-full flex flex-col sm:flex-row  text-center items-center">
                    <label for="" class="text-sm sm:text-base font-semibold inline-block w-2/4 ">مبلغ حواله</label>
                    <input type="text"
                           class="rounded-md bg-gray-100 py-1 ring-8 ring-gray-300 ring-opacity-25 outline-none Desired_amount custom_payment text-black w-3/4 inline-block">
                </div>

            </article>
    </div>
@endsection

@section('container')
    <article class="px-3 py-6">
        <form action="{{route('panel.transmission')}}" method="post"
              class="form relative flex justify-between items-center border-2 border-white rounded-md  flex-wrap  p-5 text-center  mx-auto md:w-2/3 lg:w-4/6 after:content-['انتخاب_سریع'] after:absolute after:text-white after:top-[-15px] after:bg-transparent after:backdrop-blur-3xl after:rounded-md after:px-4 sm:after:text-sm after:text-base">
            @csrf
            @foreach($services as $service)
                <label for="dollar-{{$service->id}}" data-inputID="{{$service->id}}"

                       class="dollar @if(old('service_id')==$service->id) select-dollar @endif  transition-all duration-500  p-2 w-1/3 flex justify-center items-center flex-col hover:cursor-pointer group  mb-2 sm:1/5 md:w-1/4 lg:w-1/6">
                    <img src="{{asset('src/images/dollar 1.png')}}" alt=""
                         class="dollar   w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 group-hover:cursor-pointer">

                    <p class="text-sm  font-yekan font-semibold mt-2 sm:text-base">{{$service->amount.' '.$service->name}}</p>
                    <input type="radio" id="dollar-{{$service->id}}" value="{{$service->id}}"
                           data-amount="{{$service->amount}}" name="service_id" class="hidden"
                           @if(old('service_id')==$service->id) checked @endif >
                </label>

            @endforeach
            <input type="checkbox" name="Accepting_the_rules" id="Accepting_the_rules" class="hidden">
            <input type="text" value="{{old('custom_payment')}}" name="custom_payment" id="custom_payment"
                   class="text-black hidden">
            <input type="text" value="{{old('transmission')}}" name="transmission" id="transmission"
                   class="text-black hidden">
            @foreach($banks as $bank)
                <input type="radio" name="bank" value="{{$bank->id}}" id="bank-{{$bank->id}}" class="hidden">
            @endforeach

        </form>
        <div class="text-center  ">
            <p class="font-semibold text-sm sm:text-base payment-text py-2"></p>
        </div>
    </article>

@endsection

@section('content')

    <article class="space-y-4 flex items-center justify-center flex-col  mx-auto md:w-3/4 lg:w-3/5 xl:w-2/4">
        <div class="flex items-center justify-center space-x-2 space-x-reverse text-center">
            <input type="checkbox" class="accent-yellow-600 Accepting_the_rules">
            <p class="sm:text-base text-sm  text-center ">شرایط استفاده رو با دقت مطالعه نموده و قبول دارم</p>
        </div>

      <article
          class="space-y-4 flex items-center justify-center flex-col md:flex-row md:items-center w-full md:justify-around md:space-y-0 md:mt-16">
          <div class=" flex items-center justify-start  max-w-max   rounded-md wallet ">
              <img src="{{asset('src/images/wallet.png')}}" alt="" class="w-12 h-12 bg-sky-500 rounded-md">
              <button class="bg-sky-500 py-1.5 px-2 rounded-se-md rounded-ee-md">پرداخت با کیف پول</button>
          </div>
          @foreach($banks as $bank)
              <label data-bank="{{$bank->id}}"
                     class=" flex items-center justify-start  max-w-max   rounded-md  cursor-pointer bank">
                  <img src="{{asset($bank->logo_url)}}" alt="" class="w-12 h-12 bg-sky-500 rounded-md">
                  <span class="bg-sky-500 py-1.5 px-2 rounded-se-md rounded-ee-md"> {{$bank->name}} </span>
              </label>
          @endforeach
      </article>

        <div type="button" class="loading hidden">

            <i class="fas fa-spinner fa-spin   animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"></i>
            لطفا منتظر بمانید
        </div>

    </article>
@endsection

@section('script-tag')
    <script>
        $(document).ready(function () {

            let SelectionDaller = $(".dollar");
            $(SelectionDaller).click(function () {
                $(this).addClass("dollar");
                let siblings = $(this).siblings();
                siblings.removeClass('select-dollar');
                $(this).addClass('select-dollar');
                let inputId = $(this).attr('data-inputID');
                $('#dollar-' + inputId).attr('checked', 'checked');
            });
            $('.Desired_amount').click(function () {
                $(SelectionDaller).removeClass('select-dollar')
            })
        })

        $(document).ready(function () {

                let callElementTarget = "custom_payment";

                let customPayment = $("." + callElementTarget);
                let SelectionDaller = $(".dollar");
                let dollar = commission();
                let payment_text = $('.payment-text')
                let defaultInputValue = "{{old('service_id')}}";
                if (defaultInputValue !== undefined && defaultInputValue > 0) {
                    eventService(false)
                }

                function eventService(event = true) {
                    if (event) {
                        $(SelectionDaller).click(function () {
                            let inputId = $(this).attr('data-inputID');
                            let input = $('#dollar-' + inputId);
                            let inputAmount = $(input).attr('data-amount');
                            let payment = inputAmount * dollar
                            $(payment_text).text(' مبلغ قابل پرداخت: ' + formatNumber(payment) + ' ریال ')
                            $(customPayment).val(inputAmount)
                            $("#" + callElementTarget).val('')
                        });
                    } else {
                        let input = $('#dollar-' + defaultInputValue);
                        let inputAmount = $(input).attr('data-amount');

                        let payment = inputAmount * dollar
                        $(payment_text).text(' مبلغ قابل پرداخت: ' + formatNumber(payment) + ' ریال ')
                        $(customPayment).val('')
                        $("#" + callElementTarget).val('')
                    }
                }

                eventService();
            }
        )

        $(document).ready(function () {
            let callElement = 'Accepting_the_rules';
            let acceptingRules = $('.' + callElement);
            $(acceptingRules).change(function () {
                $("#" + callElement).trigger('click')
            })
        })

        $(document).ready(function () {
            let callElementTarget = "custom_payment";
            let defaultInputTarget = "{{old('custom_payment')}}"

            let customPayment = $("." + callElementTarget);
            let payment_text = $('.payment-text')
            let dollar = commission()
            if (defaultInputTarget !== undefined && defaultInputTarget > 0) {
                $(customPayment).val(defaultInputTarget)
                eventChangeInput(false);
            }

            function eventChangeInput(event = true) {

                if (event) {
                    $(customPayment).on('input', function () {
                        let SelectionDaller = $(".dollar");
                        $.each(SelectionDaller, function (index, value) {
                            let Service = value
                            $(Service).removeClass('select-dollar')
                            $(Service).children('input').prop('checked', false)

                        })
                        let payment = $(customPayment).val();

                        if (payment <= Number("{{env('Daily_Purchase_Limit')}}")) {
                            let paymentResult = payment * dollar
                            if (payment.match(/(([0-9])?((\.)?)([0-9]{1,2}))/gm)) {
                                if(payment.includes('.'))
                                {
                                    let paymentSplit=payment.split('.')[1]
                                    if(paymentSplit.length>1)
                                    {
                                        $("#" + callElementTarget).val('')
                                        $(payment_text).text('')
                                        $(customPayment).val('')
                                        return;
                                    }
                                }

                                $("#" + callElementTarget).val(payment)
                                $(payment_text).text(' مبلغ قابل پرداخت: ' + formatNumber(paymentResult) + ' ریال ')

                            } else {
                                $("#" + callElementTarget).val('')
                                $(payment_text).text('')
                            }
                        } else {
                            $(customPayment).val('')
                            $(payment_text).text('')
                        }

                    })
                } else {

                    let SelectionDaller = $(".dollar");
                    $.each(SelectionDaller, function (index, value) {
                        let Service = value
                        $(Service).removeClass('select-dollar')
                        $(Service).children('input').prop('checked', false)

                    })
                    let payment = $(customPayment).val();
                    if (payment <= Number("{{env('Daily_Purchase_Limit')}}")) {
                            let paymentResult = payment * dollar
                        if (payment.match(/(([0-9])?((\.)?)([0-9]{1,2}))/gm)) {
                            if(payment.includes('.'))
                            {
                               let paymentSplit=payment.split('.')[1]
                                if(paymentSplit.length>1)
                                {
                                    $("#" + callElementTarget).val('')
                                    $(payment_text).text('')
                                    $(customPayment).val('')
                                    return;
                                }
                            }

                            $("#" + callElementTarget).val(payment)
                            $(payment_text).text(' مبلغ قابل پرداخت: ' + formatNumber(paymentResult) + ' ریال ')

                        } else {
                            $("#" + callElementTarget).val('')
                            $(payment_text).text('')
                        }
                    } else {
                        $(customPayment).val('')
                        $(payment_text).text('')
                    }
                }

            }


            eventChangeInput();


        })
    </script>
    <script>
        $(".wallet").click(function () {
            $('.form').submit()
            $(this).remove();
            $(".loading").removeClass('hidden');
            $(".bank").remove()
        })
        $(".bank").click(function () {
            let bankId = $(this).attr('data-bank');
            let bank = $("#bank-" + bankId);
            $(bank).attr('checked', 'checked')
            $('.form').attr('action', "{{route('panel.transferFromThePaymentGateway')}}")
            $('.form').submit()
            $(".bank").remove();

            $('.wallet').remove();
            $(".loading").removeClass('hidden');
        })
    </script>
    <script>
        function commission() {
            return "{{$dollar->DollarRateWithAddedValue()}}";
        }


        function formatNumber(number) {
            number=Math.floor(number/10000)*10000
            let string = number.toLocaleString('fa-IR'); // ۱۲٬۳۴۵٫۶۷۹
            number = string.replace(/\٬/g, ",‬");
            return number;
        }
    </script>
    <script>
        $(document).ready(function (){
            let transmission=$(".transmission");
            $(transmission).on('input',function (){
                $("#transmission").val($(this).val());
            })
        })
    </script>
@endsection
