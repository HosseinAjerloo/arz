@extends('Panel.layout.master')

@section('message-box')
    <h1 class=" border-2 border-2-white rounded-md py-3 px-3 text-sm sm:text-base font-semibold  ">
        ضمن تشکر از انتخاب <span class="text-white  font-semibold"> ساینا ارز  </span> لطفا مشکل خود را بیان کنید تا در اسرع وقت کارشناسان ما مشکل شما را بر طرف کنند.
    </h1>
@endsection

@section('container')
    <div class="py-3 px-3 w-full">
        <div class="w-1/2 md:w-1/6 mx-auto m-2">
            <a href="{{route('panel.ticket-add')}}" class="bg-sky-500 rounded-md flex items-center justify-center">
                <p class="text-center font-semibold p-2">ثبت تیکت جدید</p>
                <i class="fa-solid fa-plus w-6 h-6  pr-3"></i>
            </a>
        </div>
        <table class="table-fixed  mx-auto  w-full sm:w-2/3 md:w-3/4 lg:w-3/5 xl:w-3/6">
            <thead class="bg-sky-500 ">
            <tr class="text-white ">
                <th class=" w-1/3 py-3 font-semibold">#</th>
                <th class=" w-1/3 py-3 font-semibold">عنوان</th>
                <th class=" w-1/3 py-3 font-semibold">تاریخ</th>
                <th class=" w-1/3 py-3 font-bold">جزئیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tickets as $key=> $ticket)
                <tr class=" py-6 text-black text-sm sm:text-base">
                    <td class=" w-1/3  text-center py-2">{{$key+1}}</td>
                    <td class=" w-1/3 text-center py-2 cursor-pointer  "><a href="{{route('panel.ticket-chat',$ticket->id)}}" class="decoration-2   text-white ">{{$ticket->subject}}</a></td>
                    <td class=" w-1/3  text-center py-2">{{\Morilog\Jalali\Jalalian::forge($ticket->created_at)->format('Y/m/d H:i:s')}}</td>
                    <td class=" w-1/3  text-center py-2">{{$ticket->ticketStatus()}}</td>
                </tr>
            @endforeach


            </tbody>
        </table>
    </div>
@endsection
