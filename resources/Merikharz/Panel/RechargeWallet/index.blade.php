@extends('Panel.Layout.master')

@section('header-content')
    <section class="bg-DFEDFF h-14 flex items-center justify-center space-x-reverse space-x-2">
        <img src="{{asset('merikhArz/src/images/wallet.svg')}}" alt="" class="w-5 h-5">
        <p class="text-mini-base font-bold ">

            افزایش کیف پول </p>
    </section>
@endsection
@section('content')

    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/seke.svg')}}" alt="">
            <h1 class="text-sm">
                موجودی فعلی شما
                <span
                    class="text-mini-mini-base font-bold  @if($user->getCreaditBalance()>0) text-green-500 @else text-rose-700 @endif">
                    {{numberFormat(empty(substr($user->getCreaditBalance(),0,(strlen($user->getCreaditBalance())-1)))?0: substr($user->getCreaditBalance(),0,(strlen($user->getCreaditBalance())-1)))}}
                </span> تومان میباشد
            </h1>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 ">
            <form action="{{route('panel.wallet.charging-Preview')}}" class="space-y-5" id="form">
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">مبلغ به تومان :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full"
                           name="price">
                </div>
                @foreach($banks as $bank)
                    <input type="radio" name="bank_id" value="{{$bank->id}}" id="{{$bank->id}}" class="invisible">
                    <label class="mx-auto" for="{{$bank->id}}">
                        <span
                           class="btn cursor-pointer bg-gradient-to-b from-268832 to-80C714 flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-3 text-white rounded-lg ">
                            <span>پرداخت با {{$bank->name}}</span>
                            <img src="{{asset('merikhArz/src/images/bankkart.svg')}}" alt="" class="bg-cover w-5 h-5 ">
                        </span>
                    </label>
                @endforeach

            </form>
        </article>
    </section>

@endsection

@section('script')

    <script>
        $(document).ready(function (){
            $('.btn').click(function (){
                setTimeout(function (){
                    $('#form').submit();
                },100)

            })
        })
    </script>
@endsection
