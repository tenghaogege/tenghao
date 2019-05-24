@include("public.header")
<body>
<div class="maincont">
    <header>
        <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
        <div class="head-mid">
            <h1>产品详情</h1>
        </div>
    </header>
    <div id="sliderA" class="slider">
        @foreach($goods_imgs as $v)
            <img src="{{config("app.img_url")}}{{$v}}" />
        @endforeach
    </div><!--sliderA/-->
    <table class="jia-len">
        <tr>
            <th><strong class="orange">{{$goodsDetail->self_price}}</strong></th>
            <td>
                <input type="button" style="width:30px;height:25px;" class="n_btn_1" id="less" value="-"/>
                <input type="text" style="width:30px;height:25px;" value="1" name="" class="n_ipt" id="buy_number"/>
                <input type="button" style="width:30px;height:25px;" class="n_btn_2" id="more" value="+"/>
            </td>
        </tr>
        <tr>
            <td>
                <strong>{{$goodsDetail->goods_name}}</strong>
                <p class="hui">库存共<font color="red" id="goods_num"> {{$goodsDetail->goods_num}}</font>件</p>
            </td>
            <td align="right">
                <a href="javascript:;" class="shoucang"><span class="glyphicon glyphicon-star-empty"></span></a>
            </td>
        </tr>
    </table>
    <div class="zhaieq">
        <a href="javascript:;" class="zhaiCur">商品图片</a>
        <a href="javascript:;">商品简介</a>
        <a href="javascript:;" style="background:none;">订购列表</a>
        <div class="clearfix"></div>
    </div><!--zhaieq/-->
    <div class="proinfoList">
        <img src="{{config("app.img_url")}}{{$goodsDetail->goods_img}}" width="636" height="822" />
    </div><!--proinfoList/-->
    <div class="proinfoList">
        <p>{{$goodsDetail->goods_desc}}}</p>
    </div><!--proinfoList/-->
    <div class="proinfoList">
        <table border="1">
            @foreach($comment as $v)
            <tr>
                <td>
                    评论等级：
                </td>
                <td>
                    {{$v->comment_grand}}
                </td>
                <td>
                    评论内容：
                </td>
                <td>
                    {{$v->comment_desc}}
                </td>
            </tr>
            @endforeach
        </table>
        <input type="radio" name="goods_grade" value="1">一星
        <input type="radio" name="goods_grade" value="2">二星
        <input type="radio" name="goods_grade" value="3">三星
        <input type="radio" name="goods_grade" value="4">四星
        <input type="radio" name="goods_grade" value="5">五星

        <textarea rows="20" cols="85" id="textarea">
        </textarea>
        <input type="button" value="提交评论" class="button">
    </div><!--proinfoList/-->
    <div class="lrSub">
        <input type="hidden" value="{{$goodsDetail->goods_id}}" id="goods_id">
        <p><a href="/"><span class="glyphicon glyphicon-home"></span></a></p>
    </div>
    <div class="lrSub">
        <input type="button" value="加入购物车" id="cartAdd">
    </div>
</div><!--maincont-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/style.js')}}"></script>
<!--焦点轮换-->
<script src="{{asset('js/jquery.excoloSlider.js')}}"></script>
<script>
    $(function () {
        $("#sliderA").excoloSlider();
    });
</script>
<!--jq加减-->
<script src="{{asset("js/jquery.spinner.js")}}"></script>
<script src="{{asset('layui/layui.js')}}"></script>
<script>
    $('.spinnerExample').spinner({});
</script>
</body>
</html>
<script type="text/javascript">
    $(function(){
        layui.use(['form','layer'],function(){
            var form = layui.form;
            var layer = layui.layer;
            var goods_num = parseInt($("#goods_num").text());
            //点击加号
            $("#more").click(function() {
                var buy_number = parseInt($("#buy_number").val());
                // console.log(buy_number);
                // console.log(goods_num);
                if(buy_number>=goods_num){
                    $(this).prop('disabled',true);
                    $(this).next('input').prop('disabled',false);
                }else{
                    number=buy_number+1;
                    $("#buy_number").val(number);
                }
            });
            //点击减号
            $("#less").click(function() {
                var buy_number = parseInt($("#buy_number").val());
                if (buy_number<=1) {
                    $(this).prop('disabled',true);
                    $(this).next('input').prop('disabled',false);
                }else{
                    number = buy_number-1;
                    $("#buy_number").val(number);
                }
            });
            //失去焦点
            $("#buy_number").blur(function() {
                var buy_number = parseInt($("#buy_number").val());
                var reg=/^[1-9]\d*$/;
                if (!reg.test(buy_number)) {
                    $("#buy_number").val(1);
                }else if (buy_number<=1) {
                    $("#buy_number").val(1);
                }else if (buy_number>=goods_num) {
                    $("#buy_number").val(goods_num);
                }
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //加入购物车
            $("#cartAdd").click(function() {
                //获得当前用户的商品id
                var goods_id = $("#goods_id").val();
//                alert(goods_id);
                var buy_number = $("#buy_number").val();
//                alert(buy_number);
                $.ajax({
                    method: "post",
                    url: "/index/cart",
                    data: {goods_id:goods_id,buy_number:buy_number}
                }).done(function( res ) {
                    if(res.code==1){
                        layer.msg(res.font,{icon:res.code});
                        location.href="/index/cart";
                    }else{
                        layer.msg(res.font,{icon:res.code});
                        location.href="/user/login";
                    }
                });
            });
            //点击提交评论
            $(".button").click(function(){
                var radio = $('input:radio:checked').val();
                var textarea = $("#textarea").val();
                $.ajax({
                    method: "post",
                    url: "/index/comment",
                    data: {radio:radio,textarea:textarea}
                }).done(function( res ) {
                   if(res.code==1){
                       layer.msg(res.font,{icon:res.code});
                       window.location.reload('index/proinfo');
                   }else{
                       layer.msg(res.font,{icon:res.code});
                   }
                });
            })
        })
    })
</script>