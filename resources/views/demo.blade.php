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
    <input type="button" name="" id="btn" value="ajax请求A">
</body>
</html>
<script src="http://www.jq22.com/jquery/jquery-1.10.2.js"></script>
<script>
    $('#btn').click(function(){
        $.ajax({
            url : "http://lumen.1809a.com/ajax",
            dataType : "JSONP",
            success : function(res){}
        })

    })
</script>