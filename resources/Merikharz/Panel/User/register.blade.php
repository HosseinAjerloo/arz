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
            <form method="post" action="{{route('panel.user.register')}}" class="space-y-5"  enctype="multipart/form-data">
                @csrf
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">نام :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full" name="name" placeholder="نام"
                           value="{{old('name',$user->name)}}">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">نام خانوادگی :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full"  name="family"  placeholder="نام خانوادگی"
                           value="{{old("family",$user->family)}}">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">تلفن ثابت :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full" name="tel" placeholder="تلفن ثابت"
                           value="{{old("tel",$user->tel)}}">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">ایمیل :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full" name="email" placeholder="ایمیل"
                           value="{{old('email',$user->email)}}">
                </div>
                <div class="flex items-center space-x-reverse space-x-2 ">
                    <p class="text-black font-bold text-mini-mini-base w-24">کدملی :</p>
                    <input type="text" class="py-1.5 px-4 border-2 border-black rounded-md text-black w-full" name="national_code" placeholder="کدملی"
                           value="{{old("national_code",$user->national_code)}}">
                </div>

                <div class="flex items-center space-x-reverse space-x-2 w-full">
                    <p class="text-black font-bold text-mini-mini-base w-24">آدرس  :</p>
                </div>
                <textarea id="editor1"  name="address">{{old('address',$user->address)}}</textarea>

                <div class=" mx-auto">
                    <button class="bg-gradient-to-b from-268832 to-80C714 flex items-center justify-center space-x-reverse space-x-2 text-mini-mini-base px-4 py-2.5 text-white rounded-lg w-full">
                        <p class="text-base">ویرایش اطلاعات</p>
                        <img src="{{asset("merikhArz/src/images/userIcon.svg")}}" alt="" class="bg-cover w-5 h-5 rounded-2xl">
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
