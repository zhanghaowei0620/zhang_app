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
                用户名：<input type="text" name='username'>
            </td>
        </tr>
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
        <tr>
            <td>
                确认密码：<input type="text" name='user_pwd1'>
            </td>
        </tr>
    </table>
    <input type="button" id='btn' value = "注册">
</body>
</html>

<script>
        $('#btn').click(function(){
            var name=$('[name="username"]').val();
            var email=$('[name="email"]').val();
            var user_pwd=$('[name="user_pwd"]').val();
            var user_pwd1=$('[name="user_pwd1"]').val();
                //layer.msg('两次密码不一致')
                $.post(
                    "regAdd",
                    {name:name,email:email,user_pwd:user_pwd,user_pwd1:user_pwd1},
                    function(res){
                        console.log(res);
                        // if(res.error == 0){
                        //     alert('注册成功');
                        //     location.href='login';
                        // }else{
                        //     alert('注册失败');
                        // }
                    })

        })
</script>