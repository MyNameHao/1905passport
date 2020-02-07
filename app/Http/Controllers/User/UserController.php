<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Puser;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function reg(){
        $pdata=$_POST;
        $count=Puser::where('name',$pdata['name'])->count();
        if($count>0){
            $error1=json_encode(['errorcode'=>'0001','errmsg'=>'当前账户名已存在'],JSON_UNESCAPED_UNICODE);
            echo $error1;exit;
        }
        if($pdata['password']!=$pdata['passwords']){
            $error2=json_encode(['errorcode'=>'0002','errmsg'=>'密码与确认密码不一致'],JSON_UNESCAPED_UNICODE);
            echo $error2;exit;
        }
        $email_preg = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        if(preg_match($email_preg, $pdata['email'])==0){
            $error3=json_encode(['errorcode'=>'0003','errmsg'=>'邮箱格式有误'],JSON_UNESCAPED_UNICODE);
            echo $error3;exit;
        }
        $mobile_preg="/^1[34578]\d{9}$/";
        if(preg_match($mobile_preg,$pdata['mobile'])==0){
            $error4=json_encode(['errorcode'=>'0004','errmsg'=>'电话号码格式有误'],JSON_UNESCAPED_UNICODE);
            echo $error4;exit;
        }
        $pdata['password']=password_hash($pdata['password'],PASSWORD_BCRYPT);
        $pinfo=Puser::create($pdata);
        if($pinfo->p_id){
            $error5=json_encode(['errorcode'=>'0000','errmsg'=>'OK'],JSON_UNESCAPED_UNICODE);
            echo $error5;exit;
        }
    }
    public function login(){
        $data=$_POST;
        if(strpos($data['account'],'@')){
            $where=['email'=>$data['account']];
        }else{
            $where=['mobile'=>$data['account']];
        }
        $info=Puser::where($where)->first();
       if(!$info){
           $error6=json_encode(['errorcode'=>'0006','errmsg'=>'账户或密码有误'],JSON_UNESCAPED_UNICODE);
           echo $error6;exit;
       }
       if(!password_verify($data['password'],$info['password'])){
           $error6=json_encode(['errorcode'=>'0006','errmsg'=>'账户或密码有误'],JSON_UNESCAPED_UNICODE);
           echo $error6;exit;
       }
        $redis_key='token:user:appid:'.$info['appid'].'id:'.$info['p_id'];
        $redis_val=md5(time().$info['p_id'].$info['name']);
        Redis::set($redis_key,$redis_val);
        Redis::expire($redis_key,60480);
        $success=['errorcode'=>'0000','errmsg'=>'登录成功!','token'=>$redis_val,'id'=>$info['p_id']];
        echo json_encode($success,JSON_UNESCAPED_UNICODE);
//        echo '登录成功,您的token为:'.$redis_val;
    }
    public function gettoken(){
        $data=$_POST;
        if(strpos($data['account'],'@')){
            $where=['email'=>$data['account']];
        }else{
            $where=['mobile'=>$data['account']];
        }
        $info=Puser::where($where)->first();
        if(!$info){
            $error=json_encode(['errorcode'=>'0007','errmsg'=>'账户或appid有误'],JSON_UNESCAPED_UNICODE);
            echo $error;exit;
        }
        if($data['appid']!=$info['appid']){
            $error=json_encode(['errorcode'=>'0007','errmsg'=>'账户或appid有误'],JSON_UNESCAPED_UNICODE);
            echo $error;exit;
        }
//        $redis_key='token:user:id:'.$info['appid'];
        $redis_key='token:user:appid:'.$info['appid'].'id:'.$info['p_id'];
        $redis_val=Redis::get($redis_key);
        if($redis_val){
            echo $redis_val;
        }else{
            $error1=json_encode(['errorcode'=>'0008','errmsg'=>'未登录，请先登录再来获取'],JSON_UNESCAPED_UNICODE);
            echo $error1;exit;
        }
    }
    public function getuserinfo(){
        $data=$_POST;
        if(strpos($data['account'],'@')){
            $where=['email'=>$data['account']];
        }else{
            $where=['mobile'=>$data['account']];
        }
        $info=Puser::where($where)->first();
        if(!$info){
            $error1=json_encode(['errorcode'=>'0009','errmsg'=>'account或token有误1'],JSON_UNESCAPED_UNICODE);
            echo $error1;exit;
        }
//        $redis_key='token:user:id:'.$info['appid'];
        $redis_key='token:user:appid:'.$info['appid'].'id:'.$info['p_id'];
        $redis_token=Redis::get($redis_key);
        $data_token=$_SERVER['HTTP_TOKEN'];
        if($redis_token!=$data_token){
            $error1=json_encode(['errorcode'=>'0009','errmsg'=>'account或token有误2'],JSON_UNESCAPED_UNICODE);
            echo $error1;exit;
        }
        unset($info['p_id']);
        unset($info['password']);
        echo json_encode($info->toarray(),JSON_UNESCAPED_UNICODE);
    }
    public function github(){
        $sell='cd /wwwroot/passport && git pull';
        shell_exec($sell);
    }
    public function auth(){
        $data=json_decode(file_get_contents('php://input'),true);
        dd($data);
        $appid=Puser::where('p_id',$data['id'])->value('appid');
        $redis_key='token:user:appid:'.$appid.'id:'.$data['id'];
        $redis_token=Redis::get($redis_key);
        if($redis_token==$data['token']){
            return json_encode(['errorcode'=>'0000','errmsg'=>'OK'],JSON_UNESCAPED_UNICODE);
        }else{
            $error1=json_encode(['errorcode'=>'0009','errmsg'=>'id或token有误'],JSON_UNESCAPED_UNICODE);
            return $error1;
        }


    }
    public function verify(){
//        dump($_GET);
        $_val=$_GET['val'];
        $_key='1905qwe';
        $signature1=md5($_val.'+'.$_key);
        $signature2=$_GET['signature'];
        if($signature1==$signature2){
            echo '验签通过';
        }else{
            echo '验签失败';
        }
    }
    public function postsign(){
        $key='abc123';
        $sign1=$_POST['sign'];
        $data_json=$_POST['data'];
        $sign2=md5($data_json.$key);
        if($sign1==$sign2){
            echo '验签成功';
        }else{
            echo '验签失败';
        }
    }

}
