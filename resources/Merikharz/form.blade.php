<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="{{route('gateway-out')}}" method="post">
    @csrf

    @foreach($request->all()  as $key=> $req)
        <input type="hidden" name="{{$key}}" value="{{$req}}">
    @endforeach
</form>
</body>
</html>
<script>
    document.querySelector('form').submit();
</script>
