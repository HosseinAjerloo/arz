@include('Panel.Layout.head')
@yield('head')
<body class="overflow-x-hidden">
@include('Panel.Layout.header')
<main class="px-5 mt-4">
   @yield('content')
</main>
@include('Panel.Layout.footer')
@include('Panel.Layout.script')
@yield('script')
</body>
</html>
