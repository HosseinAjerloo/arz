@extends('Panel.Layout.master')
@section('header-content')
    <section class="bg-DFEDFF h-14 flex items-center justify-between px-4">
        <div class="flex items-center space-x-reverse space-x-2">
            <img src="{{asset('merikhArz/src/images/messageIcon.svg')}}" alt="" class="w-6 h-6">
            <p class="text-mini-base font-bold ">
                سوالات متداول
            </p>
        </div>
        <a href="{{route('panel.ticket-add')}}" class="flex items-center space-x-reverse space-x-2">
            <img src="{{asset('merikhArz/src/images/plus.svg')}}" alt="" class="w-6 h-6">
            <p class="text-mini-base font-bold underline-offset-8 underline text-sky-600 decoration-sky-600">ثبت
                تیکت</p>
        </a>

    </section>
@endsection
@section('content')

    <section class=" w-full border-2 border-black/50 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg  p-1.5">
            <div class="flex items-center space-x-reverse space-x-1">
                <h1 class="text-sm">
                    تیکت های شما
                </h1>
            </div>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 ">
            <ul class="flex items-center justify-between pb-2 border-b-2 border-b-black">
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">تیکت</p>
                </li>
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">
                        ایجاد تاریخ
                    </p>
                </li>
                <li class="text-mini-mini-base text-center  w-1/5">
                    <p class="font-medium leading-4">
                        وضعیت
                    </p>
                </li>

            </ul>
        </article>
        @foreach($tickets as $ticket)
            <article class="flex flex-col justify-start space-y-2 p-2 ">
                <ul class="flex items-center justify-between pb-2 border-b-2 border-b-black">
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <a href="{{route('panel.ticket-chat',$ticket->id)}}"
                           class="font-medium leading-4 underline underline-offset-4 decoration-sky-600 text-sky-600">
                            {{$ticket->subject??''}}
                        </a>
                    </li>
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <p class="font-medium leading-4">
                            {{\Morilog\Jalali\Jalalian::forge($ticket->created_at)->format('Y/m/d H:i:s')}}

                        </p>
                    </li>
                    <li class="text-mini-mini-base text-center  w-1/5">
                        <p class="font-medium leading-4 @if($ticket->status=='waiting_for_an_answer')text-base-font-color
                        @elseif($ticket->status=='has_been_answered')
                        text-268832
                        @else
                        text-rose-700
                        @endif">
                            {{$ticket->ticketStatus()}}
                        </p>
                    </li>

                </ul>
            </article>

        @endforeach


    </section>

@endsection

