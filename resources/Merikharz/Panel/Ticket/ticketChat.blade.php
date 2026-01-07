@extends('Panel.Layout.master')
@section('header-content')

    <section class="bg-DFEDFF h-14 flex items-center justify-center px-4">
        <div class="flex items-center space-x-reverse space-x-2">
            <p class="text-mini-base font-bold ">
                عنوان ({{$ticket->subject}})
            </p>
        </div>

    </section>

@endsection
@section('content')
    <section id="messages" class="w-full space-y-3 flex flex-col justify-center relative">
        @if($ticket->status!='closed')
            <section
                class="file-demo absolute w-full  box-border  z-[100] xl:w-1/2">
                <article class="flex justify-center items-center h-64">
                    <div class=" w-full bg-white p-2 rounded-md space-y-2 sm:w-3/5 md:w-3/6">
                        <i class="fas fa-times-circle text-sky-500 w-8 h-8 close"></i>
                        <img src="{{asset('src/images/them.jpg')}}" alt=""
                             class="h-64 w-full bg-origin-content object-contain rounded-md bg-live cursor-pointer">
                        <button class="py-1.5 px-2 rounded-md bg-sky-500 text-sm sm:text-base send">ارسال عکس</button>
                        <p class="text-rose-500 font-semibold text-sm sm:text-base">عکس ارسالی شما نمیتواند بیشتر از 3
                            مگابایت باشد</p>
                    </div>
                </article>
            </section>
        @endif
        @foreach($ticket_messages as $ticket_message)

            @if($ticket_message->user_id)
                <article class="w-full flex ">

                    <div
                        class="bg-gradient-to-b from-FFB01B to-blue-DE9408 text-mini-base p-2 leading-6 w-11/12 rounded-se-md  rounded-ee-md  ">
                        <div class="flex items-center space-x-2">
                                <span class="font-semibold flex">
                                    نام کاربری:
                                </span>
                            <h1 class="font-bold">
                                {{$ticket_message->user->fullName}}
                            </h1>
                        </div>
                        <p class=" text-black/45 text-center mb-2">{{Morilog\Jalali\Jalalian::forge($ticket_message->created_at)->format('Y/m/d')}}</p>
                        @if($ticket_message->type=='file' and $ticket_message->image)
                            <img src="{{asset($ticket_message->image->path)}}" alt="" class="w-52	">

                        @else
                            <p class="leading-relaxed w-full">
                                {!! $ticket_message->message !!}
                            </p>
                        @endif

                        <span
                            class="flex justify-end w-full text-black/45">{{Morilog\Jalali\Jalalian::forge($ticket_message->created_at)->format('h:i')}}</span>
                    </div>
                </article>
            @else
                <article class="w-full flex justify-end ">
                    <div
                        class="bg-gradient-to-b from-F4F7FB to-8EBFFC text-mini-base p-2 leading-6 w-11/12 rounded-ss-md rounded-es-md">
                        <div class="flex items-center space-x-2">

                            <h1 class="font-bold">
                              پاسخ ادمین
                            </h1>
                        </div>
                        <p class=" text-black/45 text-center mb-2">{{Morilog\Jalali\Jalalian::forge($ticket_message->created_at)->format('Y/m/d')}}</p>
                        @if($ticket_message->type=='file' and $ticket_message->image)
                            <img src="{{asset($ticket_message->image->path)}}" alt="" class="w-52	">

                        @else
                            <p class="leading-relaxed w-full">
                                {!! $ticket_message->message !!}
                            </p>
                        @endif

                        <span
                            class="flex justify-start w-full text-black/45">{{Morilog\Jalali\Jalalian::forge($ticket_message->created_at)->format('h:i')}}</span>
                    </div>
                </article>
            @endif

        @endforeach


        <input type="file" class="hidden" id="file" name="file">

    </section>
    <section class="px-2 mt-4">
        <div class="flex w-full items-center space-x-reverse space-x-2 py-2 px-2 border border-black/50">
            <img src="{{asset('merikhArz/src/images/sendIcon.svg')}}" alt="" class="cursor-pointer" id="send_message">

            <div class="w-11/12">
                <input type="text" class="w-full outline-none" id="input_message">
            </div>
            <img src="{{asset('merikhArz/src/images/fileIcon.svg')}}" alt="" class="cursor-pointer file">
        </div>
    </section>

@endsection


@section('script')

    <script>
        const messages = $('#messages');

        $('#send_message').on('click', (e) => {
            const message = $('#input_message').val();
            if (!message)
                return null;
            $.ajax({
                type: "POST",
                headers: {'X-CSRF-TOKEN': "{{csrf_token()}}"},
                url: "{{route('panel.ticket-client-message')}}",
                data: {'ticket_id': {{$ticket->id}}, 'message': message},
                success: function (response) {
                    if (response.success) {
                        console.log(response.data);
                        $('#input_message').val('');

                        const client_new_message = `<article class="w-full flex ">
                            <div class="bg-gradient-to-b from-FFB01B to-blue-DE9408 text-mini-base p-2 leading-6 w-11/12 rounded-se-md  rounded-ee-md  ">
                                <p class=" text-black/45 text-center mb-2">${response.data.jalali_date}</p>
                                <p class="leading-relaxed w-full">
                                    ${response.data.message}
                                </p>
                                <span class="flex justify-end w-full text-black/45">${response.data.hours}</span>
                            </div>
                        </article>`;
                        $('#messages').append(client_new_message);
                        messages.animate({scrollTop: messages.prop("scrollHeight")}, 500);
                        $('#input_message').css({'height': 'auto'})
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            let file = $("#file");
            $(".file").click(function () {
                $(file).trigger('click')
            })

            $(".close").click(function () {
                $('.file-demo').css({'transform': 'scale(0) translateX(-50%)', 'bottom': '10%', 'left': '50%'})
            })
            $(file).change(function () {

                let urlImage = showLiveFile();
                if (urlImage) {
                    $('.file-demo').css({'transform': 'scale(1) translateX(-50%)', 'bottom': '10%', 'left': '50%'})
                    $(".bg-live").attr('src', urlImage)
                }
            })
        })
    </script>
    <script>
        $(document).ready(function () {
            let count = 0;
            let file = $("#file");

            $('.send').click(function () {
                let fileData = $(file).get(0).files[0];
                console.log(fileData)

                let elementMessage = '';
                if (imageValidation.includes(fileData.type)) {
                    let myFormData = new FormData();
                    myFormData.append('image', fileData);
                    myFormData.append('_token', "{{csrf_token()}}")
                    myFormData.append('ticket_id', "{{$ticket->id}}")
                    $.ajax({
                        url: "{{route('panel.ticket-client-message')}}",
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        data: myFormData,
                        headers: {
                            "Accept": "application/json"
                        },
                        mimeType: "multipart/form-data",
                        success: function (response) {
                            if (response.success) {
                                let parentImage = $('#messages');
                                console.log(response.data.path);


                                const elementMessage = `<article class="w-full flex ">
                            <div class="bg-gradient-to-b from-FFB01B to-blue-DE9408 text-mini-base p-2 leading-6 w-11/12 rounded-se-md  rounded-ee-md  ">
                                        <p class=" text-black/45 text-center mb-2">${response.data.jalali_date}</p>

                                    <img src="${response.data.crs}" alt="" class="w-52" data-val="${response.data.value}">
                                <span class="flex justify-end w-full text-black/45">${response.data.hours}</span>
                            </div>
                            </article>`;
                                parentImage.append(elementMessage)
                                $('.close').trigger('click')
                                download();
                            }
                        }

                    });


                }


            })
        })
    </script>
    <script>
        let imageValidation = ['image/png', 'image/jpeg', 'image/jpg'];

        function showLiveFile() {
            let files = $("#file");
            let src;
            var _URL = window.URL || window.webkitURL;

            var image, file;
            if ((file = $(files).get(0).files[0])) {
                image = new Image();
                image.onload = function () {

                }
            }

            if (imageValidation.includes(file.type)) {
                image.src = _URL.createObjectURL(file);
                return image.src;
            }
            return false;

        }
    </script>
    <script>
        function download() {
            $(".dowbload").click(function () {
                let data = $(this).attr('data-val')

                window.location.href = data;
            })
        }

        download();
    </script>

    <script>
        document.querySelectorAll('textarea').forEach(element => {
            element.style.height = `${element.scrollHeight}px`;
            element.addEventListener('input', event => {
                event.target.style.height = 'auto';
                event.target.style.height = `${event.target.scrollHeight}px`;
            })
        })
    </script>
@endsection
