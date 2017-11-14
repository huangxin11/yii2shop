<?php
namespace frontend\controllers;
use frontend\models\Article;
use frontend\models\ArticleCategory;
use frontend\models\ArticleDetail;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use yii\web\Controller;

class IndexController extends Controller{
    /**
     * 展示页面
     */
    public function actionIndex(){
        $categorys = GoodsCategory::find()->where(['parent_id'=>0])->all();
        $article_categorys = ArticleCategory::find()->all();
        return $this->render('index',['categorys'=>$categorys,'acticle_categorys'=>$article_categorys]);
    }

    /**
     * 一级分类
     */
    public function actionOne(){
        $id=\Yii::$app->request->get('id');
        $query=Goods::find();
        $goodsCategory1 = GoodsCategory::findOne(['id'=>$id]);
//查询所有二级分类
        foreach ($goodsCategory1->children as $goodsCateogory2){
            //所有三级分类
            foreach ($goodsCateogory2->children as $goodsCateogory3){
                $query->orWhere(['goods_category_id'=>$goodsCateogory3->id]);
            }
        }
        $goods=$query->all();
        return $this->render('list',['goods'=>$goods]);
    }
    /**
     * 二级分类
     */
    public function actionTwo(){
        $id = \Yii::$app->request->get('id');
        $query = Goods::find();
        $goodsCategory2 = GoodsCategory::findOne(['id'=>$id]);
        foreach ($goodsCategory2->children as $three) {
            $query->orWhere(['goods_category_id'=>$three->id]);
        }
        $goods=$query->all();
        return $this->render('list',['goods'=>$goods]);
    }
    /**
     * 展示商品
     */
    public function actionView($id){
        $googs = Goods::find()->where(['status'=>1])->andWhere(['goods_category_id'=>$id])->andWhere(['is_on_sale'=>1])->all();
//        var_dump($googs);die;
        return $this->render('list',['goods'=>$googs]);
    }
    /**
     * 展示文章内容
     */
    public function actionContent(){
        $id=$_GET['id'];
        $model =ArticleDetail::findOne(['article_id'=>$id]);
        $article = Article::findOne(['id'=>$id]);
        return $this->render('view',['model'=>$model,'article'=>$article]);
    }
}
