<?php
namespace frontend\controllers;
use frontend\models\Article;
use frontend\models\ArticleCategory;
use frontend\models\ArticleDetail;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use frontend\models\GoodsGallery;
use frontend\models\GoodsIntro;
use yii\data\Pagination;
use yii\web\Controller;

class IndexController extends Controller{
    /**
     * 展示页面
     */
    public function actionIndex(){
        $categorys = GoodsCategory::find()->where(['parent_id'=>0])->all();
        $acticle_categorys = $acticle_categorys = ArticleCategory::find()->all();
        return $this->render('index',['categorys'=>$categorys,'acticle_categorys'=>$acticle_categorys]);
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
    /**
     * 优化商品查询
     */
    public function actionList($goods_category_id){
        $goods_category = \backend\models\GoodsCategory::findOne(['id'=>$goods_category_id]);
        if ($goods_category->depth == 2){
            $query = Goods::find()->where(['goods_category_id'=>$goods_category_id]);

        }else{
            $ids = $goods_category->children()->andWhere(['depth'=>2])->column();
//            $ids = [];
//            foreach ($result as $category){
//                $ids[] = $category->id;
//            }
            $query = Goods::find()->where(['in','goods_category_id',$ids]);
        }
        $page = new Pagination();
        $page->totalCount = $query->count();
        $page->pageSize = 2;
        $models = $query->limit($page->pageSize)->offset($page->offset)->all();
        return $this->render('list',['goods'=>$models,'page'=>$page]);
    }
    /**
     * 展示商品详情
     */
    public function actionIntro($goods_id){
        $intro = GoodsIntro::findOne(['goods_id'=>$goods_id]);
        $gallerys = GoodsGallery::find()->where(['goods_id'=>$goods_id])->all();
        $good = Goods::findOne(['id'=>$goods_id]);
//        var_dump($good);die;
//        var_dump($gallerys);die;
        return $this->render('intro',['intro'=>$intro,'gallerys'=>$gallerys,'good'=>$good]);
    }
//    public function actionDe(){
//        $redis = new \Redis();
//        $redis->connect('127.0.0.1');
//       $redis->delete('goods-category');
//    }
}
