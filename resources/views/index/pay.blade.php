@include("public.header")
<body>
<div class="maincont">
    <header>
        <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
        <div class="head-mid">
            <h1>购物车</h1>
        </div>
    </header>
    <div class="head-top">
        <img src="{{asset('images/head.jpg')}}" />
    </div><!--head-top/-->
    <div class="dingdanlist" >
        <table>
            @foreach($goodsInfo as $v)
                <div>
                    <tr goods_id="{{$v->goods_id}}" class="goods_id">
                    <tr>
                        <td class="dingimg" width="15%"><img src="{{config('app.img_url')}}{{$v->goods_img}}" /></td>
                        <td width="50%">
                            <h3>{{$v->goods_name}}</h3>
                            购买数量：<b strong class="orange" buy_number = "{{$v->buy_number}}">{{$v->buy_number}}</b>
                            <p goods_id = "{{$v->goods_id}}" id="goods_id"></p>
                            <time>下单时间：{{date('Y-m-d H:i:s',$v->create_time)}}</time>
                        </td>
                        <td colspan="3"><strong class="orange">¥{{$v->self_price*$v->buy_number}}</strong></td>
                    </tr>
                </div>

            @endforeach
            <tr>
                <td class="dingimg" width="75%" colspan="2">新增收货地址</td>
                <td align="right"><a href="/address/address"><img src="{{asset('images/jian-new.png')}}" /></a></td>
            </tr>
            <tr>
                <td class="dingimg" width="75%" colspan="2">收货地址</td>
                <input type="hidden" name="address_id" address_id="{{$is_default->address_id}}">
                <td align="right">{{$is_default->address_detail}}</td>
            </tr>
            <tr>
                <td class="dingimg" width="75%" colspan="2">支付方式</td>
                <td align="right">
                    <input type="radio" id="zhifubao" pay_type="1" name="zhifubao" checked>支付宝
                    <input type="radio" id="zhifubao" pay_type="2" name="zhifubao">微信
                </td>
            </tr>
            <tr>
                <td class="dingimg" width="75%" colspan="2">优惠券</td>
                <td align="right"><span class="hui">无</span></td>
            </tr>
            <tr>
                <td class="dingimg" width="75%" colspan="2">折扣优惠</td>
                <td align="right"><strong class="green">¥0.00</strong></td>
            </tr>
            <tr>
                <td class="dingimg" width="75%" colspan="2">抵扣金额</td>
                <td align="right"><strong class="green">¥0.00</strong></td>
            </tr>
        </table>
    </div><!--dingdanlist/-->



</div><!--content/-->

<div class="height1"></div>
<div class="gwcpiao">
    <table>
        <tr>
            <th width="10%"><a href="javascript:history.back(-1)"><span class="glyphicon glyphicon-menu-left"></span></a></th>
            <td width="50%">总计：<strong class="orange">¥{{$countPrice}}</strong></td>
            <td width="40%"><a href="javascript:;" class="jiesuan">提交订单</a></td>
            {{--success.html--}}
        </tr>
    </table>
</div><!--gwcpiao/-->
</div><!--maincont-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/style.js')}}"></script>
<script src="{{asset('layui/layui.js')}}"></script>
<!--jq加减-->
<script src="{{asset('js/jquery.spinner.js')}}"></script>
<script>
    $('.spinnerExample').spinner({});
</script>
</body>
</html>
<script>
    $(function() {
        layui.use(['layer'], function () {
            layer = layui.layer;
            //点击确认结算
            $(".jiesuan").click(function(){
                //获取购物车id
                var goods_id = '';
                $(".goods_id").each(function(index) {
                    goods_id+=$(this).attr('goods_id')+',';
                });
                goods_id = goods_id.substr(0,goods_id.length-1);
                //获取收货地址id
                var address_id = $("input[name='address_id']").attr('address_id');

                //获取支付方式的状态
                var pay_type = $("input[name='zhifubao']:checked").attr('pay_type');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: "post",
                    url: "/index/submitPay",
                    data: {goods_id:goods_id,address_id:address_id,pay_type:pay_type}
                }).done(function( res ) {
                    layer.msg(res.font,{icon:res.code,time:2000},function(){
                        if (res.code==1) {
                            location.href="/index/success/?order_id="+res.order_id

                        }
                    })
                });
            })
        })
    })
</script>