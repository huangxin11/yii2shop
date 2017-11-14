<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    public function rules()
    {
        return [
            [['name','province','city','county','address','phone','member_id'],'required'],
            ['status','boolean'],
        ];
    }
}