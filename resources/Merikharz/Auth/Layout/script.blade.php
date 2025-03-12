<script src="{{asset('merikhArz/javascript/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('merikhArz/javascript/main.js')}}"></script>
<script src="{{asset('merikhArz/src/FontAwesome/js/all.js')}}"></script>
<script>
    function close()
    {
        $(document).ready(function () {
            let toast = $('.toast');
            setTimeout(function () {
                $(toast).addClass('-translate-y-7');
                $(toast).remove();
            }, 9000)


            let closeToast = $('.close-toast');
            $(closeToast).click(function () {
                $(toast).addClass('invisible');
                $(toast).remove();
            });
        })
    }
</script>
