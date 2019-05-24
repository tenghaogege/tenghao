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
    <table class="shoucangtab">
        <tr>
            <td width="75%"><span class="hui">购物车共有：<strong class="orange">2</strong>件商品</span></td>
            <td width="25%" align="center" style="background:#fff {{asset('images/xian.jpg')}} left center no-repeat;">
                <span class="glyphicon glyphicon-shopping-cart" style="font-size:2rem;color:#666;"></span>
            </td>
        </tr>
    </table>

    <div class="dingdanlist">
        <table>
            <tr>
                <td width="100%" colspan="4"><a href="javascript:;"><input type="checkbox" name="1" id="allbox"/> 全选</a></td>
            </tr>
            @foreach($goodsInfo as $v)
            <tr>
                <td width="4%"><input type="checkbox" goods_id = "{{$v->goods_id}}" id="a"  name="2" class="box goods_number"/></td>
                <td class="dingimg" width="15%"><img src="{{config('app.img_url')}}{{$v->goods_img}}" /></td>
                <td width="50%">
                    <h3>{{$v->goods_name}}</h3>
                    库存：<b strong class="orange" goods_num = "{{$v->goods_num}}">{{$v->goods_num}}</b>
                    <p goods_id = "{{$v->goods_id}}" id="goods_id"></p>
                    <time>下单时间：{{date('Y-m-d H:i:s',$v->create_time)}}</time>
                </td>
                <td>
                    <input type="button" style="width:30px;height:25px;" class="n_btn_1" id="less" value="-"/>
                    <input type="text" style="width:30px;height:25px;" value="{{$v->buy_number}}" name="" class="n_ipt" id="buy_number"/>
                    <input type="button" style="width:30px;height:25px;" class="n_btn_2" id="more" value="+"/>
                    <a class="del">删除</a>　
                </td>
                <th colspan="4">¥<strong class="orange" self_price = {{$v->self_price}}>{{$v->buy_number*$v->self_price}}</strong></th>
            </tr>
            {{--<tr>--}}

            {{--</tr>--}}
            @endforeach
        </table>
    </div><!--dingdanlist/-->

    <div class="height1"></div>
    <div class="gwcpiao">
        <table>
            <tr>
                <th width="10%"><a href="javascript:history.back(-1)"><span class="glyphicon glyphicon-menu-left"></span></a></th>
                <td width="40%">总计：¥<strong class="orange" id="counTail">0</strong></td>
                <td width="40%"><a href="javascript:;" class="jiesuan" id="confirmCount">确认结算</a></td>
            </tr>
        </table>
    </div><!--gwcpiao/-->
</div><!--maincont-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/style.js')}}"></script>
<!--jq加减-->
<script src="{{asset('js/jquery.spinner.js')}}"></script>
<script src="{{asset('layui/layui.js')}}"></script>
<script>
    $('.spinnerExample').spinner({});
</script>
</body>
</html>
<script>
    $(function(){
        layui.use(['layer'],function(){
            var layer = layui.layer;
            //全选点击事件
            $("#allbox").click(function() {
                var status = $(this).prop('checked');
                $(".box").prop('checked',status);
                //获取总价
                countTotal();
            });

            //复选框点击事件
            $(".box").click(function() {
                countTotal();
            });

            //加号点击事件
            $(document).on('click', '.n_btn_2', function() {
                var _this = $(this);
                var buy_number = parseInt(_this.prev('input').val());
//                 console.log(buy_number);
                var goods_num = _this.parent().prev().find('b').attr('goods_num');
//                 alert(goods_num);
                if (buy_number>=goods_num) {
                    _this.prop('disabled',true);
                }else{
                    buy_number = buy_number+1;
                    _this.prev('input').val(buy_number);
                    _this.siblings("input[class='n_btn_1']").prop('disabled',false);
                }
                //调用方法使数据库中得购买数量发生改变
                var goods_id = _this.parent().prev().find('p').attr('goods_id');
//                console.log(goods_id);
                changeBuyNumber(goods_id,buy_number);

                // 获取小计
                getSubTotal(_this,buy_number);

                //给当前复选框选中
                boxChecked(_this);
                //重新获取总价
                countTotal();
            });

            //减号点击事件
            $(document).on('click', '.n_btn_1', function() {
                var _this = $(this);
                var buy_number = parseInt(_this.next("input").val());
//                alert(buy_number);
                var goods_id = _this.parent().prev().find('p').attr('goods_id');
                if (buy_number<=1) {
                    _this.prop("disabled",true);
                }else{
                    buy_number-=1;
                    _this.next("input").val(buy_number);
                    _this.siblings("input[class='n_btn_2']").prop('disabled',false);
                }
                //调用方法使数据库中得购买数量发生改变
                changeBuyNumber(goods_id,buy_number);
                // 获取小计
                getSubTotal(_this,buy_number);
                //给当前复选框选中
                boxChecked(_this);
                //重新获取总价
                countTotal();
            });

            //失去焦点
            $(document).on('blur', '#buy_number', function() {
                var _this = $(this);
                var buy_number = parseInt(_this.val());//购买数量
//                console.log(buy_number);
                var goods_num = _this.parent().prev().find('b').attr('goods_num');//库存
                var goods_id = _this.parent().prev().find('p').attr('goods_id');
                //验证是否符合规则
                var reg = /^[1-9]\d*$/;
                //验证是否小于等于1
                //验证是否大于库存
                var reg = /^[1-9]\d*$/;
                if(!reg.test(buy_number)){
                    _this.val(1);
                }else if(buy_number>goods_num){
                    _this.val(goods_num);
                }else if(buy_number<1){
                    _this.val(1);
                }else{
                    _this.val(buy_number);
                }
                //调用方法使数据库中得购买数量发生改变
                changeBuyNumber(goods_id,buy_number);
                // 获取小计
                getSubTotal(_this,buy_number);
                //给当前复选框选中
                boxChecked(_this);
                //重新获取总价
                countTotal();
            });

            //删除商品
            $(document).on('click', '.del', function() {
                var _this = $(this);
                var goods_id = _this.parent().prev().find('p').attr('goods_id');
                layer.confirm('是否确认删除?', {icon: 3, title:'提示'}, function(index){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        method:"post",
                        url:"/index/cartDel",
                        data:{goods_id:goods_id},
                    }).done(function(res){
                        if(res.code==1){
                            window.location.reload('index/cart');
                        }else{
                            layer.msg(res.font,{icon:res.code});
                        }
                    })
                });
            });

            //批量删除
//            $(document).on('click', '#clearCart', function() {
//                var goods_id = '';
//                var _box = $('.box');
//                _box.each(function(index) {
//                    if ($(this).prop('checked')==true) {
//                        goods_id+=$(this).parents("tr").attr('goods_id')+',';
//                    }
//                });
//                goods_id = goods_id.substr(0,goods_id.length-1);
//                // console.log(goods_id);
//                layer.confirm('是否确认删除?', {icon: 3, title:'提示'}, function(index){
//                    $.post(
//                        "{:url('Cart/cartDel')}",
//                        {goods_id:goods_id},
//                        function(res){
//                            layer.msg(res.font,{icon:res.code});
//                            if (res.code==1) {
//                                location.href="{:url('Cart/cartList')}";
//                                layer.close(index);
//                            }
//                        },
//                        'json'
//                    );
//                });
//            });

            //点击确认结算
            $(document).on('click', '#confirmCount', function() {
                //获取选中得复选框得 商品得id
                var goods_id = '';

                $("input[name='2']:checked").each(function(){
                        goods_id += $(this).attr('goods_id')+',';
                })
                if (goods_id=='') {
                    layer.msg('请至少选择一件商品',{icon:2});
                    return false;
                }
                goods_id = goods_id.substr(0,goods_id.length-1);
//                alert(goods_id);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method:"post",
                    url:"/index/pay",
                    data:{goods_id:goods_id},
                }).done(function(res){
                    if(res.code==1){
                        layer.msg(res.font,{icon:res.code,time:2000},function(){
                            location.href="/index/pay?goods_id="+goods_id;
                        })
                    }
                })
            });
            //调用方法使数据库中得购买数量发生改变
            function changeBuyNumber(goods_id,buy_number){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url:"/index/changeBuyNumber",
                    method:'post',
                    data:{goods_id:goods_id,buy_number:buy_number},
                    async:false,
                    success:function(res){
                        //错误给出提示 成功不给提示
                        if (res.code==2) {
                            layer.msg(res.font,{icon:res.code});
                        }
                    }
                })
            }

            // 获取小计
            function getSubTotal(_this,buy_number){
//                var aa = $('.orange');
                var self_price = parseInt(_this.parent('td').siblings('th').children('strong').attr('self_price'));
                var total = self_price*buy_number;

                _this.parent('td').siblings('th').children('strong').text(total);
            }

            //给当前复选框选中
            function boxChecked(_this){
                _this.parent('td').siblings('td').children("input[id='a']").prop("checked",true);
            }

            //获取总价
            function countTotal(){
                var goods_id = '';
                $("input[name='2']:checked").each(function(){
                    goods_id += $(this).attr('goods_id')+',';
                })
                goods_id = goods_id.substr(0,goods_id.length-1);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method:"post",
                    url:"/index/counTotal",
                    data:{goods_id:goods_id}
                }).done(function(res){
                    $('#counTail').text(res);
                })
            }

        })
    })
</script>