@include('Auth.Layout.head')
@include('Auth.Layout.script')

<body class="overflow-x-hidden">

@include('Auth.Layout.header')
@include('Alert.Toast.warning')
@include('Alert.Toast.success')
@yield('header-content')

<main class="mt-4 px-5 ">
    @yield('content')
</main>
@include('Auth.Layout.footer')
@yield('script-tag')

</body>
</html>
