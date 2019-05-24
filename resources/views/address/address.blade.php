@include("public.header")
<body>
<div class="maincont">
    <header>
        <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
        <div class="head-mid">
            <h1>收货地址</h1>
        </div>
    </header>
    <div class="head-top">
        <img src="{{asset('images/head.jpg')}}" />
    </div><!--head-top/-->
    <table class="shoucangtab">
        <tr>
            <td width="75%"><a href="/address/shipping" class="hui"><strong class="">+</strong> 新增收货地址</a></td>
            <td width="25%" align="center" style="background:#fff   {{asset('images/xian.jpg')}} left center no-repeat;"><a href="javascript:;" class="orange">删除信息</a></td>
        </tr>
    </table>

    <div class="dingdanlist">
        <table>
            @foreach($addressInfo as $v)
                @if($v['is_default']==1)
                    <div class="address" style="border: 1px solid red;">
                        <tr>
                            <td width="50%">
                                <input type="hidden" address_id="{{$v['address_id']}}">
                                <h3>{{$v['address_name']}}</h3>
                                <time>{{$v['province']}}{{$v['city']}}{{$v['area']}}</time>
                            </td>
                            <td width="25%"><button  class="default">设为默认</button></td>
                            <td align="right"><a href="address.html" class="hui"><span class="glyphicon glyphicon-check"></span> 修改信息</a></td>
                        </tr>
                    </div>
                @else
                    <div class="address" address_id="{$v['address_id']}">
                        <tr>
                            <td width="50%">
                                <input type="hidden" address_id="{{$v['address_id']}}">
                                <h3>{{$v['address_name']}}</h3>
                                <time>{{$v['province']}}{{$v['city']}}{{$v['area']}}</time>
                            </td>
                            <td width="25%"><button  class="default">设为默认</button></td>
                            <td align="right"><a href="address.html" class="hui"><span class="glyphicon glyphicon-check"></span> 修改信息</a></td>
                        </tr>
                    </div>
                @endif
            @endforeach
        </table>
    </div><!--dingdanlist/-->
@include("public.footer")
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
            layer = layui.layer;
            //点击设置为默认
            $(document).on('click', '.default', function() {
                var _this = $(this);
                address_id = _this.parent("td").next().siblings("td").find("input[type='hidden']").attr("address_id");
//                console.log(address_id)
                layer.confirm('是否设置为默认?', {icon: 3, title:'提示'}, function(index){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        method: "post",
                        url: "/address/addressdefault",
                        data:{address_id:address_id}
                    }).done(function( res ) {
                        if(res.code==1){
                            layer.msg(res.font,{icon:res.code});
                            window.location.reload('address/address');
                        }else{
                            layer.msg(res.font,{icon:res.code});
                        }
                    });
                })
            });
        })
    })
</script>