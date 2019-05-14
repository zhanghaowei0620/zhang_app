<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="http://www.jq22.com/jquery/jquery-1.10.2.js"></script>
    <script src="layui/layui.js"></script>
</head>
<body>
    <table>
        <tr>
            <td>
                邮箱：<input type="text" name='email'>
            </td>
        </tr>
        <tr>
            <td>
                密码：<input type="text" name='user_pwd'>
            </td>
        </tr>
    </table>
    <input type="button" id='btn' value = "登陆">
</body>
</html>

<script>
        $('#btn').click(function(){
            var email=$('[name="email"]').val();
            var user_pwd=$('[name="user_pwd"]').val();
                //layer.msg('两次密码不一致')
                $.post(
                    "logindo",
                    {email:email,user_pwd:user_pwd},
                    function(res){
                        console.log(res);
                        // if(res.msg =='登陆成功'){
                        //     layer.msg(res.res);
                        //     location.href='logindo';
                        // }else{
                        //     layer.msg(res.res);
                        // }
                    })

        })
</script>