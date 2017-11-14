<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    public function getCategory(){
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
}
