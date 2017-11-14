<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{

    public function getArticle(){
        return $this->hasMany(Article::className(),['article_category_id'=>'id']);
    }
}