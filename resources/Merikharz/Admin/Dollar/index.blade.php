@extends('Panel.Layout.master')

@section('content')

    <section class=" w-full border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <img src="{{asset('merikhArz/src/images/seke.svg')}}" alt="">
            <h1 class="text-sm">
                قیمت فعلی  <span class="text-mini-mini-base font-bold text-rose-700">{{$dollar_price}}</span> ریال میباشد
            </h1>
        </header>
        <article class="flex flex-col justify-start space-y-2 p-2 w-full">
            <form method="post" action="{{route('panel.admin.dollar-price-submit')}}" class="space-y-5"  enctype="multipart/form-data">
                @csrf
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">قیمت دلار(ریال) :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full" name="dollar_price" placeholder="قیمت دلار(ریال)"
                           value="{{old('dollar_price')}}">
                </div>


                <div class="flex items-center space-x-reverse space-x-2 w-full">
                    <p class="text-black font-bold text-mini-mini-base w-24">متن توضیح :</p>
                </div>
                <textarea id="editor1"  name="description">{{old('description')}}</textarea>

                <div class=" mx-auto">
                    <button class="bg-gradient-to-b from-268832 to-80C714 flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-2 text-white rounded-lg w-full">
                        <p class="">ثبت نرخ جدید</p>
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
