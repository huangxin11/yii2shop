<?php
namespace backend\models;
use yii\base\Model;

class KeyForm extends Model{
    public $name;
    public $sn;
    public $maxprice;
    public $minprice;
    public function attributeLabels()
    {
        return [
            'name'=>'商品名称',
            'sn'=>'商品货号',
            'maxprice'=>'最大价格',
            'minprice'=>'最小价格',
        ];
    }
    public function rules()
    {
        return [
            [['name','sn','maxprice','minprice'],'safe'],
        ];
    }
}