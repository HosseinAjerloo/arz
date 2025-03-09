@include('Panel.Layout.head')
@include('Panel.Layout.script')

@yield('head')

<body class="overflow-x-hidden">
@include('Panel.Layout.header')
@include('Alert.Toast.warning')
@include('Alert.Toast.success')
@yield('header-content')
<main class="px-5 mt-4">
   @yield('content')
</main>
@include('Panel.Layout.footer')

@yield('script')
</body>
</html>
