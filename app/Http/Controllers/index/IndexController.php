<?php
namespace App\Http\Controllers\Index;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Cache; #控制器中使用缓存
class IndexController extends Controller{
    //首页
    public function index(){
//        $session = session('userInfo');
//        $user_id = $session['user_id'];
////        $where=[
////            'user_id'=>$user_id,
////        ];
//        $goods_id = DB::table('goods')->select('goods_id')->orderBy('create_time','desc')->get()->toArray();
//        $goods_id = array_column($goods_id,'goods_id');
//        $goodsWhere=[
//            [ 'is_up','=',1],
//            ['cart_status','=',1],
//            ['user_id','=',$user_id],
//        ];
////        dd(array($goods_id));
////        DB::connection()->enableQueryLog();#开启执行日志
//        $goodsInfo=DB::table('goods')
//            ->select('goods.goods_id','goods_name','self_price','market_price','goods_img','buy_number','goods_num','cart.create_time')
//            ->join('cart', 'goods.goods_id', '=', 'cart.goods_id')
//            ->where($goodsWhere)
//            ->whereIn('goods.goods_id',$goods_id)
//            ->orderBy("goods.goods_id",'desc')
//            ->get();
//        dd($goodsInfo);
        //搜索
        $goods_name = Request()->goods_name;
//		dd($goods_name);
        $goodsWhere = [];
        if ($goods_name) {
            $goodsWhere[] =['goods_name','like',"%$goods_name%"];
        }
        // 分类条件
        $where=[
            'pid'=>0
        ];
        //分类
        $cateInfo = DB::table('category')->where($where)->get();
//		dd($cateInfo);
        //商品
        $goodsWhere=[
            'is_up'=>1,
        ];
        $goodsInfo = DB::table('goods')->where($goodsWhere)->get();
        $session = request()->session()->get('userInfo');
//		轮播图
        $where=[
            'goods_id'=>15
        ];
        $goodsImgs = DB::table('goods')->where($where)->first();
        $goods_imgs = $goodsImgs->goods_imgs;
        $goods_imgs = rtrim($goods_imgs,'|');
        $goods_imgs = explode('|',$goods_imgs);
        return view('/index/index',compact('goodsInfo','cateInfo','session','goods_imgs','goodsImgs'));
    }
    //详情页
    public function proinfo($goods_id=0){
//        $goodsDetail = cache('goodsDetail'.$goods_id);
//        dd($goodsDetail);
//        if (!$goodsDetail){
//            echo 1;die;
            $where=[
                'goods_id'=>$goods_id,
            ];
            $goodsDetail = DB::table('goods')->where($where)->first();
//            $goodsDetail = cache(['goodsDetail'.$goods_id=>$goodsDetail],60*12);
//            dd($goodsDetail);
//        }
//        $goods_imgs = cache('goods_imgs'.$goods_id);
//        dd($goods_imgs);
//        if (!$goods_imgs){
//            echo 1;die;
            $goods_imgs = $goodsDetail->goods_imgs;
            $goods_imgs = rtrim($goods_imgs,'|');
            $goods_imgs = explode('|',$goods_imgs);
//            $goods_imgs = cache(['goods_imgs'.$goods_id=>$goods_imgs],60*12);
//            dd($goods_imgs);
            $session = session("userInfo");
            $user_id = $session['user_id'];
            $commentWhere=[
                'user_id'=>$user_id,
            ];
//        Cache::put('comment','1',60*12);  #写入缓存（key，value，time）
//        $comment = Cache::get('comment');
//        dump($comment);die;
            $comment = DB::table('comment')->where($commentWhere)->orderBy('comment_grand','desc')
                ->get();

        return view('/index/proinfo',compact('goodsDetail','goods_imgs','comment'));
    }
    //加入购物车
    public function cart(){
        $goods_id = request()->goods_id;
        $buy_number = request()->buy_number;
        if(request()->isMethod('post')) {
            if (request()->session()->get('userInfo')==null){
                return ['code'=>2,'font'=>'请先登陆'];
            }else{
//              dd(request()->session()->get('userInfo'));
                $where=[
                    'goods_id'=>$goods_id,
                ];
                $goodsInfo = DB::table('goods')->where($where)->first();
//                dd($goods_num);
                if (empty($goods_id)) {
                    return ['code'=>2,'font'=>'请选择一个商品'];
                }else if (empty($goodsInfo)) {
                    return ['code'=>2,'font'=>'你选择的商品已下架'];
                }
                if (empty($buy_number)) {
                    return ['code'=>2,'font'=>'请选择你要购买的数量'];
                }
//            dd(session('userInfo'));
                //加入购物车
                if (session('userInfo')){
                    //如果登陆了就存数据库
                    $addCartDb = $this->addCartDb($goods_id,$buy_number);
                    if ($addCartDb['code']==1){
                        return ['code'=>1,'font'=>'加入购物车成功'];
                    }else{
                        return ['code'=>2,'font'=>'加入购物车失败！'];
                    }
                }
            }
        }elseif (request()->isMethod('get')){
            $session = session('userInfo');
            $user_id = $session['user_id'];
            $where=[
                'user_id'=>$user_id,
            ];
            $goods_id = DB::table('cart')->select('goods_id')->where($where)->orderBy('create_time','desc')->get()->toArray();
            $goods_id = array_column($goods_id,'goods_id');
            $goodsInfo = $this->getCartInfo($goods_id,$user_id);
            return view('index/cart',compact('goodsInfo'));
        }
    }
    //把购物车数据存数据库
    public function addCartDb($goods_id,$buy_number){
        $session = session('userInfo');
        $where=[
            'user_id'=>$session['user_id'],
            'goods_id'=>$goods_id,
        ];
        //查询购物车中是否有当前用户的商品数据
        $cartInfo = DB::table('cart')->where($where)->first();
//        dd($cartInfo);
        //如果是空的就走添加
        if (empty($cartInfo)){
            $addWhere=[
                'user_id'=>$session['user_id'],
                'goods_id'=>$goods_id,
                'buy_number'=>$buy_number,
            ];
            $addWhere['create_time'] = time();
            $addWhere['update_time'] = time();
            $res = DB::table("cart")->insert($addWhere);
            if ($res){
                return ['code'=>1,'font'=>'主人，我在购物车等你哦！'];
            }else{
                return ['code'=>2,'font'=>'主人，你怎么把我弄丢了！'];
            }
        }else{
            //如果不是空的就修改数据库中的库存
            $where=[
                'goods_id'=>$goods_id,
            ];
            $goodsNum = DB::table('goods')->where($where)->first();
            if ($cartInfo->buy_number>$goodsNum->goods_num){
                return ['code'=>2,'msg'=>'主人，货物都卖光了'];
            }else{
                $cartWhere=[
                    'goods_id'=>$goods_id,
                    'user_id'=>$session['user_id'],
                ];
                $updateWhere=[
                    'buy_number' => $cartInfo->buy_number+$buy_number,
                ];
                $updateWhere['update_time'] = time();
                $res = DB::table('cart')->where($where)->update($updateWhere);
                if ($res){
                    return ['code'=>1,'font'=>'主人，我在购物车等你哦！'];
                }else{
                    return ['code'=>2,'font'=>'主人，你怎么把我弄丢了！'];
                }
            }
        }
    }
    //购物车列表展示
    public function getCartInfo($goods_id,$user_id){
        $goodsWhere=[
            [ 'is_up','=',1],
            ['cart_status','=',1],
            ['user_id','=',$user_id],
        ];
//        dd(array($goods_id));
//        DB::connection()->enableQueryLog();#开启执行日志
        $goodsInfo=DB::table('goods')
            ->select('goods.goods_id','goods_name','self_price','market_price','goods_img','buy_number','goods_num','cart.create_time')
            ->join('cart', 'goods.goods_id', '=', 'cart.goods_id')
            ->where($goodsWhere)
            ->whereIn('goods.goods_id',$goods_id)
            ->orderBy("goods.goods_id",'desc')
            ->get();
//        dd($goodsInfo);
//        dd(DB::getQueryLog());   //获取查询语句、参数和执行时间
        if(!empty($goodsInfo)){
            return $goodsInfo;
        }else{
            return false;
        }
    }
    //购物车删除
    public function cartDel(){
        $goods_id = request()->goods_id;
        $session = session('userInfo');
        $user_id = $session['user_id'];
        $where = [
            'goods_id'=>$goods_id,
            'user_id'=>$user_id,
        ];
        $updateWhere=[
            'cart_status'=>2
        ];
        $del = DB::table('cart')->where($where)->update($updateWhere);
        if ($del){
            return ['code'=>1,'font'=>'删除成功'];
        }else{
            return ['code'=>2,'font'=>'删除失败'];
        }
    }
    //更改数据库中数据
    public function changeBuyNumber(){
        $goods_id = request()->goods_id;
        $buy_number = request()->buy_number;
        if (empty($goods_id)) {
            fail('请至少选择一件商品');
        }
        if (empty($buy_number)) {
            fail('购买数量不能为空');
        }
        $changeBuyNumberDb = $this->changeBuyNumberDb($goods_id,$buy_number);
    }
    //更改数据库中数据方法
    public function changeBuyNumberDb($goods_id,$buy_number){
        $session = session('userInfo');
        $user_id = $session['user_id'];
        //检测库存
        $where=[
            'goods_id'=>$goods_id,
        ];
        $goodsInfo = DB::table('goods')->where($where)->first();
        if ($goodsInfo) {
            $where=[
                'goods_id'=>$goods_id,
                'user_id'=>$user_id,
            ];
            $updateInfo = [
                'buy_number'=>$buy_number,
                'update_time'=>time()
            ];
            $result = DB::table('cart')->where($where)->update($updateInfo);
            if ($result) {
                return ['code'=>'1','font'=>'修改数量成功'];
            }else{
                return ['code'=>'2','font'=>'发生未知错误，导致你的商品数量更改失败'];
            }
        }else{
            return ['code'=>2,'font'=>'商家商品库存不足，更改失败'];
        }
    }
    //获取总价
    public function counTotal(){
        $goods_id = request()->goods_id;
        $goods_id=explode(',',$goods_id);
        $session = session('userInfo');
        $user_id = $session['user_id'];
        //从数据库中获取总价
        $goodsWhere=[
            [ 'is_up','=',1],
            ['user_id','=',$user_id],
        ];
        $goodsInfo=DB::table('cart')
            ->select('self_price','buy_number')
            ->join('goods', 'cart.goods_id', '=', 'goods.goods_id')
            ->where($goodsWhere)
            ->whereIn('cart.goods_id',$goods_id)
            ->get();
        $count = 0;
        foreach ($goodsInfo as $k => $v) {
            $count+=$v->buy_number*$v->self_price;
        }
        echo $count;
        }
    //把购物车数据存到cookie
//    public function addCartCookie($goods_id,$buy_number){
////        echo 123;die;
//        $session = session('userInfo');
//        $user_id = $session['user_id'];
//        //先判断购物车表中是否被添加过这条数据
//        $where=[
////            'user_id'=>$user_id,
//            'goods_id'=>$goods_id
//        ];
////        dd($where);
//        $cartInfo = DB::table('cart')->where($where)->first();
////        dd($cartInfo);
//        $cart_str = request()->cookie('cartInfo');
//        dd($cart_str);
//        //如果没有值 就向cookie中添加数据 如果有值就向cookie中追加
//        if (empty($cart_str)){
////            如果没有值 就向cookie中添加数据
//            $goodsNum = DB::table('goods')->where($where)->first();
////            dd($goodsNum);
//            if ($cartInfo->buy_number>$goodsNum->goods_num) {
//                return ['code' => 2, 'msg' => '主人，货物都卖光了'];
//            }
//            $data=[
//                'goods_id'=>$goods_id,
//                'buy_number'=>$buy_number,
//                'create_time'=>time(),
//            ];
//            $value = response('Hello Cookie')->cookie('cartInfo', $data, 604800);
//            dd($value);
//            return ['code'=>1,'font'=>'添加成功'];
//        }else{
//            //如果cookie这条数据就累加
//            $value = request()->cookie('cartInfo');
//            dd($value);
//        }
//    }
    //全部商品
    //确认结算
    //确认订单页面
    public function pay(){
        if (request()->isMethod('post')) {
            $session = session('userInfo');
//            dd($session);
            if (!$session){
                return ['code'=>2,'font'=>'请先登陆'];
            }else{
                return ['code'=>1,'font'=>'提交成功'];
            }
        } elseif (request()->isMethod('get')) {
            $session = session('userInfo');
            if ($session==null){
                return redirect('user/login');
            }else{
                $goods_id = request()->goods_id;
//                dd($goods_id);
                $goods_id=explode(',',$goods_id);
//                    dd($goods_id);
                $user_id = $session['user_id'];
//                dd($user_id);
                $goodsWhere=[
                    [ 'is_up','=',1],
                    ['cart_status','=',1],
                    ['user_id','=',$user_id],
                ];
//            dd($goodsWhere);
                $goodsInfo=DB::table('goods')
                    ->select('goods.goods_id','goods_name','self_price','market_price','goods_img','buy_number','goods_num','cart.create_time')
                    ->join('cart', 'goods.goods_id', '=', 'cart.goods_id')
                    ->where($goodsWhere)
                    ->whereIn('goods.goods_id',$goods_id)
                    ->orderBy("goods.goods_id",'desc')
                    ->get();
//                dd($goodsInfo);
                //获取商品总价
                $countPrice = 0;
                foreach ($goodsInfo as $k => $v) {
                    $countPrice+=$v->self_price*$v->buy_number;
                }
//                dd($countPrice);
                //获取收货地址
                $where = [
                    'is_default'=>1,
                    'user_id'=>$user_id,
                ];
//                dd($where);
                $is_default = DB::table('address')->where($where)->first();
//                dd($is_default);
//        dd(DB::getQueryLog());   //获取查询语句、参数和执行时间
                return view('index/pay',compact('goodsInfo','countPrice','is_default'));
            }

        }
    }
    //执行确认订单
    public function submitPay(){
        $session = session('userInfo');
        $user_id = $session['user_id'];
        //判断是否登陆
        if ($session==null){
            return redirect('user/login');
        }
        $goods_id = request()->goods_id;
        $goods_id=explode(',',$goods_id);
        $address_id = request()->address_id;
        $pay_type = request()->pay_type;
        if (empty($goods_id)) {
            return ['code'=>'2','font'=>'请选择一件商品'];
            exit;
        }
        if (empty($pay_type)) {
            return ['code'=>'2','font'=>'请选择一种支付方式'];
            exit;
        }
        if (empty($address_id)) {
            return ['code'=>'2','font'=>'请选择一种收获方式'];
            exit;
        }
        //开启事务 把订单信息存入订单表
        DB::beginTransaction();
        try{
            //订单号
            $orderInfo['order_no']=$this->getOrderNo();
            //结算的商品数据
            $goodsWhere=[
                [ 'is_up','=',1],
                ['user_id','=',$user_id],
            ];
            $goodsInfo=DB::table('cart')
                ->select('goods.goods_id','goods_name','self_price','goods_img','buy_number')
                ->join('goods', 'cart.goods_id', '=', 'goods.goods_id')
                ->where($goodsWhere)
                ->whereIn('cart.goods_id',$goods_id)
                ->get();
            //把对象改为数组
            $goodsInfo = json_decode(json_encode($goodsInfo), true);
//            dd($goodsInfo);
            // 结算的商品的总价
            $count = 0;
            foreach ($goodsInfo as $k => $v) {
                $count+=$v['buy_number']*$v['self_price'];
            }

            $orderInfo['order_no']=$this->getOrderNo();
            $orderInfo['order_amount']=$count;
            $orderInfo['pay_type']=$pay_type;
            $orderInfo['user_id']=$user_id;
            $orderInfo['create_time'] = time();
            $orderInfo['update_time'] = time();
            $res = DB::table('order')->insert($orderInfo);
            if(!$res){
                DB::rollback();
                return ['code'=>'2','font'=>'订单信息写入失败'];
            }
            //拿到订单表的id
            $order_id = DB::getPdo()->lastInsertId();
            //把订单商品信息写入订单详情表
            foreach ($goodsInfo as $k => $v) {
                $res1 = $this->checkGoodsNum($goods_id,0,$v['buy_number'],2);
                if (!$res1) {
                    DB::rollback();
                    return ['code'=>'2','font'=>$v['goods_name'].'库存不足,请重新选择'];
                }
                $goodsInfo[$k]['order_id']=$order_id;
                $goodsInfo[$k]['user_id']=$user_id;
                $goodsInfo[$k]['create_time'] = time();
                $goodsInfo[$k]['update_time'] = time();
            }
            $res2 = DB::table('order_detail')->insert($goodsInfo);
            if (!$res2) {
                DB::rollback();
                return ['code'=>'2','font'=>'写出订单表失败'];
            }
            //把订单的收货地址存入收货地址表
            $where=[
                'address_id'=>$address_id
            ];
            $addressInfo = DB::table("address")->select('address_name','address_tel','address_email','address_detail','province','city','area')->where($where)->get();
            if (empty($addressInfo)) {
                DB::rollback();
                return ['code'=>'2','font'=>'收货地址不存在'];
            }
            $addressInfo = json_decode(json_encode($addressInfo), true);
//            dd($addressInfo);
            foreach ($addressInfo as $k=>$v){
                $addressInfo[$k]['order_id']=$order_id;
                $addressInfo[$k]['user_id']=$user_id;
                $addressInfo[$k]['create_time'] = time();
                $addressInfo[$k]['update_time'] = time();
            }
            $res3 = DB::table('order_address')->insert($addressInfo);
            if (!$res3) {
                DB::rollback();
                return ['code'=>'2','font'=>'写入收货地址失败'];
            }
            //清空购物车结算的商品数据
            $where=[
                'user_id'=>$user_id,
            ];
            $updateWhere=[
                'cart_status'=>2,
            ];
            $res4 = DB::table('cart')->where($where)->whereIn('goods_id',$goods_id)->update($updateWhere);
            if (!$res4) {
                DB::rollback();
                return ['code'=>'2','font'=>'清空购物车商品数据失败'];
            }
            //减少商品表中商品的库存
            $goodsInfos=DB::table('cart')
                ->select('goods.goods_id','goods_name','self_price','goods_img','buy_number','goods_num')
                ->join('goods', 'cart.goods_id', '=', 'goods.goods_id')
                ->where($goodsWhere)
                ->whereIn('cart.goods_id',$goods_id)
                ->get();
            $goodsInfos = json_decode(json_encode($goodsInfos), true);
//            dd($goodsInfos);
            $goodsInfo_id = array_column($goodsInfos,'goods_id');
            foreach ($goodsInfos as $k => $v) {
                $updatewhere=[
                    'goods_num'=>$v['goods_num']-$v['buy_number'],
                ];
                //bug2 一但修改 所有数据都修改 $updatewhere 没有起作用
                $res5 = DB::table('goods')->whereIn('goods_id',$goodsInfo_id)->update($updatewhere);
                if (!$res5) {
                    DB::rollback();
                    return ['code'=>'2','font'=>'减少库存数量失败'];
                }
            }
            DB::commit();
            return $arr = ['code'=>1,'font'=>'订单提交成功','order_id'=>$order_id];
            echo json_encode($arr);
        }catch(\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return ['code'=>'2','font'=>'订单信息写入失败'];
        }
        return ['code'=>'1','font'=>'订单信息成功'];
    }
    //成功提交订单
    public function success(){
        $order_id = request()->order_id;
//        dd($order_id);
        session(['order'=>$order_id]);
//        echo session('order');
        $session = session("userInfo");
        $user_id = $session['user_id'];
        $where=[
            'user_id'=>$user_id,
            'order_id'=>$order_id,
        ];
        $orderInfo = DB::table('order')->where($where)->get();
        $orderInfo = json_decode(json_encode($orderInfo), true);
        $addressInfo = $this->getAddressInfo();
        return view('index/success',compact('orderInfo','addressInfo'));
    }
    //所有商品页面
    public function allshop(){
        return view('index/allshop');
    }
    //用户管理
    public function user(){
        return view('index/user');
    }
    //退出
    public function quit(){
        request()->session()->forget('userInfo');
        return redirect('/');
    }
    //订单号 规则 时间戳加随机五位数
    public function getOrderNo(){
        return time().rand(11111,99999);
    }
    // 检测数量是否超过库存
    public function checkGoodsNum($goods_id,$old_buy_number,$buy_number,$type=1){
        $where = [
            'goods_id' => $goods_id
        ];
        $goods_num = DB::table('goods')->where($where)->value('goods_num');
        if(($old_buy_number+$buy_number)>$goods_num){
            $n = $goods_num-$old_buy_number;
            if ($type==1) {
                return ['code'=>2,'font'=>'库存不足，最多还能购买'.$n.'件'];
            }else{
                return false;
            }
        }else{
            return true;

        }
    }
    //处理收货地址
    public function getAddressInfo(){
        $session = session('userInfo');
        $user_id = $session['user_id'];
        $where=[
            'user_id'=>$user_id,
            'address_status'=>1
        ];
        // dump($where);exit;
//        $area_model = model('Area');
        $addressInfo = DB::table('address')->where($where)->orderBy('is_default','asc')->get()->map(function ($value) {
            return (array)$value;
        })->toArray();
//         dump($addressInfo);exit;
        if (!empty($addressInfo)) {
            foreach ($addressInfo as $k => $v) {
                //处理收货地址的省市区
                $addressInfo[$k]['province']=DB::table('area')->where(['id'=>$v['province']])->value('name');
                $addressInfo[$k]['city']=DB::table('area')->where(['id'=>$v['city']])->value('name');
                $addressInfo[$k]['area']=DB::table('area')->where(['id'=>$v['area']])->value('name');
            }
            return $addressInfo;
        }else{
            return false;
        }
    }
    //缓存
    public function memcache(){
        Cache::put('memcache','data',1);  #写入缓存（key，value，time）
        dd(Cache::get('memcache'));  #获取缓存
    }
    //评论
    public function comment(){
        $session = session("userInfo");
        $user_id = $session['user_id'];
        $radio = request()->radio;
        $textarea = request()->textarea;
        $data = [
            'user_id'=>$user_id,
            'comment_grand'=>$radio,
            'comment_desc'=>$textarea
        ];
        $comment = DB::table('comment')->insert($data);
        if ($comment){
            return ['code'=>1,'font'=>'评论成功'];
        }else{
            return ['code'=>2,'font'=>'评论失败'];
        }
    }
    //pc zhifu
    public function pcpay(){
//        echo 111;die;

        $order_id=session('order');
//            dd($order_id);
        $session = session("userInfo");
        $user_id = $session['user_id'];
//        dd($user_id);
        $where=[

            'order_id'=>$order_id,
            'user_id'=>$user_id
        ];
//        dd($where);
        $info=DB::table('order')->where($where)->first();
//        dd($info);
        $config=config('alipay');
//        dd($config);
        require_once app_path('alipay\pagepay\service\AlipayTradeService.php');

        require_once app_path('alipay\pagepay\buildermodel\AlipayTradePagePayContentBuilder.php');

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $info->order_no;

        //订单名称，必填
        $subject ='滕浩呀';

        //付款金额，必填
        $total_amount = $info->order_amount;

        //商品描述，可空
        $body = '爱你';

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        var_dump($response);

    }
}

?>