<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Puser extends Model
{
    //
    protected $table = 'p_user';//指定表名
    protected $primaryKey = 'p_id';//指定自增id
    protected $guarded = ['passwords'];//黑名单
    public $timestamps = false;//自动时间戳  true是开启，false是关闭
}
