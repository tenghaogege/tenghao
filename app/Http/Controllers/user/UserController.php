<?php
namespace App\Http\Controllers\user;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Cache; #控制器中使用缓存
class UserController extends Controller{	
	//注册
	public function register(){
		return view('user/register');
	}
	//执行注册
	public function registerDo(){		
		$register = request()->except('_token');
		$register['user_pwd'] = md5($register['user_pwd']);
		$register['create_time'] = time();
		$user_email = $register['user_email'];
		$where=[
			'user_email'=>$user_email,
		];
		$checkEmail = DB::table('user')->where($where)->first();
		if ($checkEmail) {
			return ['code'=>3,'font'=>'邮箱已被注册'];
		}
		$data = DB::table('user')->insert($register);
    	if ($data) {
    		return ['code'=>1,'font'=>'注册成功'];
    	}else{
    		return ['code'=>2,'font'=>'注册失败'];
    	} 	
	}
	//验证码是否正确
	public function code(){
		$session = request()->session()->get('code');
		$user_code = request()->user_code;
		if ($session!=$user_code) {
			return ['code'=>2,'font'=>'验证码错误'];
		}else{
			return ['code'=>1,'font'=>'验证码正确'];
		}
	}
	//调用邮箱
	public function sendEmail(){
		$user_email = request()->user_email;
    	$email = $this->send($user_email);
    	if ($email['code']==1){
            return ['code'=>1,'font'=>'邮箱验证码发送成功'];
        }else{
            return ['code'=>2,'font'=>'邮箱验证码发送失败'];
        }
	}
    //发动邮箱
    public function send($user_email){
        //在闭包函数内部不能直接使用闭包函数外部的变量  使用use导入闭包函数外部的变量$email
        $code = rand(1111,9999);
        \Mail::raw($code ,function($message)use($user_email){
        	// echo 123;die;
 			// 设置主题
        $message->subject("滕浩科技有限公司");//邮件主题
        // 设置接收方
        $message->to($user_email);
    });
        $failures = \Mail::failures();
        if ($failures==[]){
            $email = request()->session()->put('code',$code);
            return ['code'=>1,'font'=>'邮箱验证码发送成功'];
        }else{
            return ['code'=>2,'font'=>'邮箱验证码发送失败'];
        }

	}
	//登陆和密码错误三次锁定一小时
	public function login(){
		if(request()->isMethod('post')){
        	$user_email = request()->user_email;
        	$user_pwd = request()->user_pwd;
        	$where=[
        		'user_email'=>$user_email,
        	];
        	$data = DB::table('user')->where($where)->first();
        	$data = collect($data);
        	// dd($data);
        	$error_num = $data['error_num'];
	        //最后一次错误时间
	        $last_error_time = $data['last_error_time'];
	        //当前时间
	        $nowTime = time();
	        // dd($nowTime);
	     	//修改条件
	        $updateWhere=[
	            'user_id'=>$data['user_id']
	        ];
	        if ($data) {
	        	//如果密码与数据库密码一致
	        	if (md5($user_pwd) == $data['user_pwd']) {
	        		//判断账号密码是否在锁定中
	        		if ($error_num>=3&&$last_error_time>3600) {
	        			$lastTime = 60-ceil(($nowTime-$last_error_time)/60);
       					return ['code'=>2,'msg'=>'账号已锁定，请您于'.$lastTime.'分钟后登录！'];
	        		}else{
	        			// 密码正确  错误次数清零 错误时间为空
	        			$updateInfo = [
	        				'error_num'=>0,
	        				'last_error_time'=>null,
	        			];
	        			$res = DB::table('user')->where($updateWhere)->update($updateInfo);
	        			$userInfo = [
	        				'user_email'=>$data['user_email'],
	        				'user_pwd'=>$data['user_pwd'],
	        				'user_id'=>$data['user_id'],
	        			];
	        			request()->session()->put('userInfo',$userInfo);
                    	// dd(request()->session()->get('userInfo'));	
                    	return ['code'=>1,'font'=>'登陆成功'];        		
	        		}
	        	}else{
	        		//如果密码与数据库密码不一致
	        		if ($nowTime-$last_error_time>3600) {
       				//错误次数改为1 错误时间改为当前时间
	        			$updateInfo = [
	        				'error_num'=>1,
	        				'last_error_time'=>$nowTime,
	        			];
	        			$res=Db::table('user')->where($updateWhere)->update($updateInfo);
	        			return ['code'=>2,'font'=>'您的账号或密码发生未知错误，您还有2次机会可以登录'];
	        		}else{
	        			if ($error_num>=3&&$last_error_time>3600) {
	        				$last_time = 60-ceil(($nowTime-$last_error_time)/60);
	        				return ['code'=>2,'font'=>'账号已锁定，请您于'.$last_time.'分钟后登录！'];
	        			}else{
	        				$updateInfo = [
	        					'error_num'=>$error_num+1,
	        					'last_error_time'=>$nowTime,
	        				];
	        				$res=Db::table('user')->where($updateWhere)->update($updateInfo);
	        				$num = 3-($error_num+1);
                        	if($num > 0){
                        		return ['code'=>2,'font'=>'账号或密码错误，你还有'.$num.'次机会登录'];
                        	}else{
                        		return ['code'=>2,'font'=>'账号已锁定，请您于一小时后登录！'];
                        	}
	        			}
	        		}
	        		return ['code'=>2,'font'=>'账号或密码错误'];
	        	}
	        }else{
	        	return ['code'=>2,'font'=>'手机号或密码错误，请重新输入'];
	        }
    	}elseif (request()->isMethod('get')){
			return view('user/login');
    	}
	}
	//登陆验证邮箱是否存在
	public function checkUser(){
		$data = request()->except('_token');
		$user_email = $data['user_email'];
		$where = [
			'user_email'=>$user_email,
		];
		$data = DB::table('user')->where($where)->first();
		if ($data) {
			return ['code'=>1,'font'=>'邮箱可用'];
		}else{
			return ['code'=>2,'font'=>'邮箱未注册,请先去注册'];
		}
	}
}

?>