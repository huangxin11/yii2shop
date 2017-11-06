<?php
namespace backend\models;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;

class Goods extends ActiveRecord{
    public $code;
        public function attributeLabels()
    {
        return [
            'name'=>'商品名称',
            'sn'=>'货号',
            'logo'=>'商标',
            'goods_category_id'=>'所属分类',
            'brand_id'=>'所属品牌',
            'market_price'=>'市场售价',
            'shop_price'=>'商品售价',
            'stock'=>'库存',
            'is_on_sale'=>'是否上架',
            'status'=>'状态',
            'sort'=>'排序',
            'create_time'=>'添加时间',
            'view_times'=>'浏览次数',
        ];
    }
        public function rules()
    {
        return [
            [['name','sn','goods_category_id','brand_id','logo','market_price','shop_price','stock','is_on_sale','status','sort'],'required'],
            //上传文件规则验证
//            ['imgFile','file','extensions'=>['jpg','gif','png']],
            //验证码
            ['code','captcha'],
        ];
    }


}

