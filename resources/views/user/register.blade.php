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
     <!-- <form action="" method="post" class="reg-login" onsubmit="false"> -->
     {{csrf_field()}}
      <h3>已经有账号了？点此<a class="orange" href="/user/login">登陆</a></h3>
      <div class="lrBox">
       <div class="lrList"><input type="text" name="user_email" id="userMobile" placeholder="输入邮箱号" /></div>
       <div class="lrList2"><input type="text" name="user_code" id="user_code" placeholder="输入短信验证码" />
         <button id="code">获取验证码</button>
       </div>
       <div class="lrList"><input type="password" name="user_pwd" id="user_pwd" placeholder="设置密码" /></div>
       <div class="lrList"><input type="password" name="user_pwd1" id="conpwd" placeholder="再次输入密码" /></div>
      </div><!--lrBox/-->
      <div class="lrSub">
       <input type="button" value="立即注册" />
      </div>
     <!-- </form> -->
     @include('public.footer')
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/style.js')}}"></script>
   <script src="{{url('layui/layui.js')}}"></script>
  </body>
</html>
<script>
  $(function(){
     layui.use(["layer","form"],function(){
            layer = layui.layer;
         //点击输入邮箱
         $("#userMobile").blur(function(){
            var user_email = $("#userMobile").val();
            var reg = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/; 
            if (user_email=='') {
               layer.msg('邮箱不能为空');
               return false;
            }else if (!reg.test(user_email)) {
               layer.msg('请输入正确格式的邮箱');
               return false;
            }
         })
         //点击输入密码
         $("#user_pwd").blur(function() {
            var user_pwd = $("#user_pwd").val();
            var reg=/^[0-9a-zA-Z]{6,16}$/;
            if (user_pwd=='') {
               layer.msg('密码不能为空');
            }else if (!reg.test(user_pwd)) {
               layer.msg('请输入正确格式密码');
            }
         });
         //确认密码
         $('#conpwd').blur(function(){
            var _this = $(this);
            var user_pwd = $('#user_pwd').val();
            var conpwd = _this.val();
            if(user_pwd!=conpwd){
                layer.msg('您俩次输入的密码不一致哦！');
            }
         })
          //点击发送短信验证码
          $("#code").click(function() {
            var user_email = $("#userMobile").val();
            var minusTime=60;
            $('#code').text(minusTime+'s');
            _time = setInterval(less,1000); 
            $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
            });

            $.ajax({
                 method: "post",
                 url: "sendEmail",
                 data: {user_email:user_email},
                    dataType: "json",
              }).done(function( res ) {
                 if(res.code==1){
                     layer.msg(res.font,{icon:res.code});
                 }else{
                     layer.msg(res.font,{icon:res.code});
                 }
            });
          });
          //输入邮箱验证码 
          $("#user_code").blur(function() {
            var user_code = $("#user_code").val();
            if (user_code=='') {
               layer.msg('请输入验证码');
               return false;
            }
            $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
            });
            //验证验证码
            var falg=true;
            $.ajax({
               async:false,
               method: "post",
               url: "code",
               data: {user_code:user_code}
            }).done(function( res ) {
               if(res.code==2){
                  layer.msg(res.font,{icon:res.code});
                  falg=false;
               }else if(res.code==1){
                  layer.msg(res.font,{icon:res.code});
                  falg = falg;
               }
            });
            if (!falg) {
               return falg;
            }
          });

         //点击立即注册
         $("input[type='button']").click(function() {
            //点击输入邮箱
            var user_email = $("#userMobile").val();
            var reg = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/; 
            if (user_email=='') {
               layer.msg('邮箱不能为空');
               return false;
            }else if (!reg.test(user_email)) {
               layer.msg('请输入正确格式的邮箱');
               return false;
            }
            //验证码的值
            var user_code = $("#user_code").val();
            if (user_code=='') {
               layer.msg('请输入验证码');
               return false;
            }
            //输入密码
            var user_pwd = $("#user_pwd").val();
            var reg=/^[0-9a-zA-Z]{6,16}$/;
            if (user_pwd=='') {
               layer.msg('密码不能为空');
               return false;
            }else if (!reg.test(user_pwd)) {
               layer.msg('请输入正确格式密码');
               return false;
            }
            //确认密码
            var conpwd = $("#conpwd").val();
            if(user_pwd!=conpwd){
                layer.msg('您俩次输入的密码不一致哦！');
                return false;
            }
            $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
            });
            //验证验证码
            var falg=true;
            $.ajax({
               async:false,
               method: "post",
               url: "code",
               data: {user_code:user_code}
            }).done(function( res ) {
               if(res.code==2){
                  layer.msg(res.font,{icon:res.code});
                  falg=false;
               }
            });
            if (!falg) {
               return falg;
            }
            // 注册
            var falg=true;
            $.ajax({
               async:false,
               method: "post",
               url: "registerDo",
               data: {user_email:user_email,user_code:user_code,user_pwd:user_pwd}
            }).done(function( res ) {
               if (res.code==1) {
                  layer.msg(res.font,{icon:res.code});
                  location.href="/user/login";
                  falg=falg;
               }else if (res.code==2) {
                  layer.msg(res.font,{icon:res.code});
                  falg=false;
               }else if (res.code==3) {
                  layer.msg(res.font,{icon:res.code});
                  falg=false;
               }
            });
            if (!falg) {
               return falg;
            }
         });
          //倒计时
          function less(){
            var num = parseInt($('#code').text());
            if(num == 0){
                $('#code').text('获取');
                clearInterval(_time);
                // 倒计时结束才能点击
                $('#code').css('pointer-events','auto');
            }else{
                num-=1;
                // console.log(num);
                $('#code').text(num+'s');
                // 倒计时期间不能点击
                $('#code').css('pointer-events','none');
            }
          };
      })
  })
</script>
