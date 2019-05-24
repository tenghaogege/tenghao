@include("public.header")
<body>
<div class="maincont">
    <div class="head-top">
        <img src="/images/head.jpg" />
        <dl>
            <dt><a href="/index/user"><img src="/images/touxiang.jpg" /></a></dt>
            <dd>
                <h1 class="username">三级分销终身荣誉会员</h1>
                <ul>
                    <li><a href="/index/allshop"><strong>34</strong><p>全部商品</p></a></li>
                    <li><a href="javascript:;"><span class="glyphicon glyphicon-star-empty"></span><p>收藏本店</p></a></li>
                    <li style="background:none;"><a href="javascript:;"><span class="glyphicon glyphicon-picture"></span><p>二维码</p></a></li>
                    <div class="clearfix"></div>
                </ul>
            </dd>
            <div class="clearfix"></div>
        </dl>
    </div><!--head-top/-->
    <form action="/" method="get" class="search">
        <input type="text" class="seaText fl"  name="goods_name"/>
        <input type="submit" value="搜索" class="seaSub fr" />
    </form><!--search/-->
    <ul class="reg-login-click">
        @if($session==null)
            <li><a href="/user/login">登录</a></li>
            <li><a href="/user/register" class="rlbg">注册</a></li>
            <div class="clearfix"></div>
        @endif
    </ul><!--reg-login-click/-->
    <div id="sliderA" class="slider">
        @foreach($goods_imgs as $v)
            <a href="/index/proinfo/{{$goodsImgs->goods_id}}"><img src="{{config("app.img_url")}}{{$v}}" width="469" height="100"/></a>
        @endforeach
    </div><!--sliderA/-->
    <ul class="pronav">
        @foreach($cateInfo as $v)
            <li><a href="">{{$v->cate_name}}</a></li>
        @endforeach
        <div class="clearfix"></div>
    </ul><!--pronav/-->
    <div class="prolist">
        @foreach($goodsInfo as $v)
            <dl>
                <dt><a href="/index/proinfo/{{$v->goods_id}}"><img src="{{config('app.img_url')}}{{$v->goods_img}}" width="100" height="100" /></a></dt>
                <dd>
                    <h3><a href="/index/proinfo/{{$v->goods_id}}">{{$v->goods_name}}</a></h3>
                    <div class="prolist-price"><strong>¥{{$v->self_price}}</strong> <span>¥{{$v->market_price}}</span></div>
                    <div class="prolist-yishou"><span>5.0折</span> <em>库存：{{$v->goods_num}}</em></div>
                </dd>
                <div class="clearfix"></div>
            </dl>
        @endforeach
    </div><!--prolist/-->
    <div class="joins"><a href="fenxiao.html"><img src="images/jrwm.jpg" /></a></div>
    <div class="copyright">Copyright &copy; <span class="blue">这是就是三级分销底部信息</span></div>

@include("public.footer")
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/style.js"></script>
    <!--焦点轮换-->
    <script src="js/jquery.excoloSlider.js"></script>
    <script>
        $(function () {
            $("#sliderA").excoloSlider();
        });
    </script>
</body>
</html>