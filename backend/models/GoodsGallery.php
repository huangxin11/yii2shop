<?php
namespace backend\models;
use yii\db\ActiveRecord;

class GoodsGallery extends ActiveRecord{
    public function attributeLabels()
    {
        return [
            'path'=>'',
            'goods_id'=>'',
        ];
    }

    public function rules()
    {
        return [
            [['goods_id','path'],'required'],
        ];
    }
    public static function getGoodsGallery($goods_id){
       $html = '';
        $gallerys = self::find()->where(['goods_id'=>$goods_id])->all();
        foreach ($gallerys as $k=>$gallery){
            $html.='<li>
             <a href="javascript:void(0);" rel="{gallery: \'gal1\', smallimage: \''.\Yii::$app->params['backend_domain'].$gallery->path.'\',largeimage: \''.\Yii::$app->params['backend_domain'].$gallery->path.'\'}"><img src="'.\Yii::$app->params['backend_domain'].$gallery->path.'"></a>
             </li>';
        }

        return $html;
    }

}
