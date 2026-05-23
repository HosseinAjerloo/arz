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

</body>
</html>
<script>
    function postRefId(refIdValue) {
        var form = document.createElement("form");
        form.setAttribute("method", "POST");
        //form.setAttribute("action", "https://pgw.bpm.bankmellat.ir/pgwchannel/startpay.mellat");
        form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");
        form.setAttribute("target", "_self");
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("name", "RefId");
        hiddenField.setAttribute("value", refIdValue);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
    postRefId("{{$token}}");
</script>
