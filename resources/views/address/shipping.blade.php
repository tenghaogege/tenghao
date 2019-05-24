@include('public.header')
<div class="maincont">
    <header>
        <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
        <div class="head-mid">
            <h1>收货地址</h1>
        </div>
    </header>
    <div class="head-top">
        <img src="/images/head.jpg" />
    </div><!--head-top/-->
    <form action="/order/addresslist" method="get" class="reg-login" onsubmit="return false">
        <div class="lrBox">
            <div class="lrList"><input type="text" name="address_name" id="address_name" placeholder="收货人" /></div>
            <div class="lrList"><input type="text" name="address_detail" id="address_detail" placeholder="详细地址" /></div>
            {{--<div class="address" address_id="{$v.address_id}">--}}
            <div class="lrList">
                <select name="province" id="province">
                    <option value=''>请选择</option>
                    @foreach($provinceInfo as $k=>$v)
                        <option value="{{$v->id}}">{{$v->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="lrList">
                <select name="city" id="city">
                    <option value=''>请选择</option>
                </select>
            </div>
            <div class="lrList">
                <select name="area" id="area">
                    <option value=''>请选择</option>
                </select>
            </div>
            <div class="lrList"><input type="text" placeholder="手机号"  name="address_tel" id="address_tel"/></div>
            {{--<div class="lrList2"><input type="text" placeholder="设为默认地址" /><button class="default">设为默认</button></div>--}}
        </div><!--lrBox/-->
        <div class="lrSub">
            <input type="button" value="保存" class="submit" />
        </div>
    </form><!--reg-login/-->

    <div class="height1"></div>
    @include('public.footer')
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
</div><!--maincont-->
<script>
    $(function() {
        layui.use(['layer'], function () {
            layer = layui.layer;
            //点击下拉菜单
            $('select').change(function () {
                var _this=$(this);
                var id=_this.val();
                var _option="<option value=''>请选择</option>";
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post(
                    'getArea',
                    {id:id},
                    function (res) {
                        if (res.code==1){
                            for(var i in res['areaInfo']){
                                _option += "<option value='"+res['areaInfo'][i]['id']+"'> "+res['areaInfo'][i]['name']+
                                    "</option>";
                            }
                            _this.parent('div').next().find('select').html(_option);
                        }
                    }
                )
            });
            //点击确认添加
            $(document).on('click', '.submit', function() {
                province = $("#province").val();
                city = $("#city").val();
                area = $("#area").val();
                address_name = $("#address_name").val();
                address_detail = $("#address_detail").val();
                address_tel = $("#address_tel").val();
                // console.log(obj.is_default);
                if (province=='') {
                    fail('请选择一个完整的配送地址');
                    return false;
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: "post",
                    url: "/address/shippingDo",
                    data: {province:province,city:city,area:area,address_name:address_name,address_detail:address_detail,address_tel:address_tel}
                }).done(function( res ) {
                    if(res.code==1){
                        layer.msg(res.font,{icon:res.code});
                    }else{
                        layer.msg(res.font,{icon:res.code});
                    }
                });
            });
        })
    })

</script>
