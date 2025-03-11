@extends('Panel.Layout.master')

@section('content')

    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/seke.svg')}}" alt="">
            <h1 class="text-sm">
                موجودی فعلی شما <span class="text-mini-mini-base font-bold text-rose-700">{{numberFormat($user->getCreaditBalance()/10)}}</span> تومان میباشد
            </h1>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 w-full">
            <form method="post" action="{{route('panel.ticket-add-submit')}}" class="space-y-5"  enctype="multipart/form-data">
                @csrf
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">عنوان تیکت :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full" name="subject" placeholder="عنوان تیکت"
                    value="{{old('subject')}}">
                </div>

                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">ارسال عکس :</p>
                    <input type="file" name="image" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 w-full">
                    <p class="text-black font-bold text-mini-mini-base w-24">متن پیام :</p>
                </div>
                <textarea id="editor1"  name="message">{{old('message')}}</textarea>

                <div class=" mx-auto">
                    <button class="bg-gradient-to-b from-268832 to-80C714 flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-2 text-white rounded-lg w-full">
                        <p class="">ثبت تیکت</p>
                        <img src="{{asset('merikhArz/src/images/bankkart.svg')}}" alt="" class="bg-cover w-5 h-5 ">
                    </button>
                </div>
            </form>
        </article>
    </section>

@endsection
@section('script')

    <script>
        CKEDITOR.replace( 'editor1' ,{
            versionCheck: false,
            language: 'fa',
            removeButtons: 'Image,Link,Source,About',
        });

    </script>
@endsection
