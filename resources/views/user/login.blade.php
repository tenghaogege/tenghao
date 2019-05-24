@include('public.header')
  <body>
    <div class="maincont">
     <header>
      <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
      <div class="head-mid">
       <h1>会员注册</h1>
      </div>
     </header>
     <div class="head-top">
      <img src="{{asset('images/head.jpg')}}" />
     </div><!--head-top/-->
     <form action="" method="post" class="reg-login" onsubmit="return false">
     {{csrf_field()}}
      <h3>还没有三级分销账号？点此<a class="orange" href="/user/register">注册</a></h3>
      <div class="lrBox">
       <div class="lrList"><input type="text" name="user_email" id="user_email" placeholder="请输入邮箱号" /></div>
       <div class="lrList"><input type="password" name="user_pwd" id="user_pwd" placeholder="请输入密码" /></div>
      </div><!--lrBox/-->
      <div class="lrSub">
       <input type="button" value="提交">
      </div>
     </form><!--reg-login/-->
    @include("public.footer")
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/style.js')}}"></script>
    <script src="{{url('layui/layui.js')}}"></script>
  </body>
</html>
<script>
  $(function(){
      layui.use(['layer'],function(){
         layer = layui.layer;
            //验证账号
            $("#user_email").blur(function(){
               var user_email = $("#user_email").val();
               var reg = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/; 
               if (user_email=='') {
                  layer.msg('请输入邮箱号');
                  return false;
               }else if (!reg.test(user_email)) {
                  layer.msg('请输入正确格式的邮箱');
                  return false;
               }
            });
            //点击输入密码
            $("#user_pwd").blur(function() {
               var user_pwd = $("#user_pwd").val();
               var reg=/^[0-9a-zA-Z]{6,16}$/;
               if (user_pwd=='') {
                  layer.msg('密码不能为空');
                  return false;
               }else if (!reg.test(user_pwd)) {
                  layer.msg('请输入正确格式密码');
                  return false;
               }
            });
            $(".lrSub").click(function() {
               var user_email = $("#user_email").val();
               var reg = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/; 
               if (user_email=='') {
                  layer.msg('请输入邮箱号');
                  return false;
               }else if (!reg.test(user_email)) {
                  layer.msg('请输入正确格式的邮箱');
                  return false;
               }

               var user_pwd = $("#user_pwd").val();
               var reg=/^[0-9a-zA-Z]{6,16}$/;
               if (user_pwd=='') {
                  layer.msg('密码不能为空');
                  return false;
               }else if (!reg.test(user_pwd)) {
                  layer.msg('请输入正确格式密码');
                  return false;
               }

               $.ajaxSetup({
                  headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
               });
               //验证邮箱是否被注册
               var falg=true;
               $.ajax({
                  async:false,
                  method: "post",
                  url: "checkUser",
                  data: {user_email:user_email}
               }).done(function( res ) {
                     if (res.code==2) {
                        layer.msg(res.font,{icon:res.code});
                        falg = false;
                     }
               });
               if (!falg) {
                  return falg;
               }
               //登陆
               var falg=true;
               $.ajax({
                  async:false,
                  method: "post",
                  url: "login",
                  data: {user_email:user_email,user_pwd:user_pwd}
               }).done(function( res ) {
                  if (res.code==1) {
                     layer.msg(res.font,{icon:res.code});
                     location.href="/";
                     falg = falg;
                  }else if(res.code==2){
                     layer.msg(res.font,{icon:res.code});
                     falg = false;
                  }
               });
               if (!falg) {
                  return falg;
               }
            });
      })
  })
</script>
