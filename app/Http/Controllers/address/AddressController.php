<?php
namespace App\Http\Controllers\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Cache; #控制器中使用缓存
class AddressController extends Controller{
    public function address(){
        $addressInfo = $this->getAddressInfo();
        return view('address/address',compact('addressInfo'));
    }
    //新增收货地址
    public function shipping(){
        //获取所有的省份信息作为下拉菜单的值
        $provinceInfo = $this->getAreaInfo(0);
//        dd($provinceInfo);
        return view('address/shipping',compact('provinceInfo'));
    }
    public function getAreaInfo($pid){
        $where=[
            'pid'=>$pid
        ];
        $province = DB::table('area')->where($where)->get();
//        dd($province);
        if (!empty($province)) {
            return $province;
        }else{
            return false;
        }
    }
    //执行添加收货地址
    public function shippingDo(){
        $session = session('userInfo');
        $user_id = $session['user_id'];
        $data = request()->except("_token");
        $data['user_id'] = $user_id;
        $data['create_time'] = time();
        $data['address_email'] = 123456;
        $addressInfo = DB::table('address')->insert($data);
        if ($addressInfo){
            return ['code'=>1,'font'=>'添加成功'];
        }else{
            return ['code'=>2,'font'=>'添加失败'];
        }
    }
    //设为默认
    public function addressdefault(){
        $address_id = request()->address_id;
        $session = session("userInfo");
        $user_id = $session['user_id'];
        $updateWhere = [
            'user_id'=>$user_id
        ];
        $where=[
            'user_id'=>$user_id,
            'address_id'=>$address_id,
        ];
        $defaultWhere=[
            'is_default'=>1
        ];
        DB::beginTransaction();
        $addressInfo = DB::table('address')->where($updateWhere)->update(['is_default'=>2]);
        $res = DB::table('address')->where($where)->update($defaultWhere);
//        dd($res);
        if ($addressInfo!==false&&$res) {
            DB::commit();
            if ($res) {
                return ['code'=>1,'font'=>'设置成功'];
            }else{
                return ['code'=>2,'font'=>'设置失败'];
            }
        }else{
            DB::rollback();
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
    //获取市县
    public function getArea(){
        $id = request()->id;
        if (empty($id)) {
            return ['code'=>2,'font'=>'请选择一件商品'];
        }
        $areaInfo = $this->getAreaInfo($id);
//        dd($areaInfo);
        return ['areaInfo'=>$areaInfo,'code'=>1];

    }
}