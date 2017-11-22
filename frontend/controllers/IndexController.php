<?php
namespace frontend\controllers;
use frontend\models\Article;
use frontend\models\ArticleCategory;
use frontend\models\ArticleDetail;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use frontend\models\GoodsGallery;
use frontend\models\GoodsIntro;
use frontend\models\SphinxClient;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class IndexController extends Controller{
    /**
     * 展示页面
     */
    public function actionIndex(){
        $categorys = GoodsCategory::find()->where(['parent_id'=>0])->all();
        $acticle_categorys = $acticle_categorys = ArticleCategory::find()->all();
        $content = $this->render('index',['categorys'=>$categorys,'acticle_categorys'=>$acticle_categorys]);
        file_put_contents('index.html',$content);
        return $content;
    }
    //中文分词搜索测试
    public function actionSearch(){
//        var_dump($_GET);die;
        $serarch = $_GET['serarch'];
        if ($serarch == '请输入商品关键字'){
            return $this->redirect(['index/index']);
        }
        $cl = new SphinxClient();
        $cl->SetServer ( '127.0.0.1', 9312);//设置sphinx的searchd服务信息
        $cl->SetConnectTimeout ( 10 );//超时
        $cl->SetArrayResult ( true );//结果以数组形式返回
// $cl->SetMatchMode ( SPH_MATCH_ANY);
        $cl->SetMatchMode ( SPH_MATCH_EXTENDED2);//设置匹配模式
        $cl->SetLimits(0, 1000);//设置分页
        $info = $serarch;//查询关键字
        //进行查询   Query(查询关键字,使用的索引)
        $res = $cl->Query($info, 'goods');//shopstore_search
//print_r($cl);
        //print_r($res);
        if(isset($res['matches'])){
            //查询到结果
            $ids = ArrayHelper::map($res['matches'],'id','id');

            //var_dump($ids);
            $query = Goods::find();
            $page = new Pagination();
            $page->totalCount = $query->count();
            $page->pageSize = 2;
            $models = $query->where(['in','id',$ids])->limit($page->pageSize)->offset($page->offset)->all();

//            $models = $query->limit($page->pageSize)->offset($page->offset)->all();
            return $this->render('list',['goods'=>$models,'page'=>$page]);
        }else{
        }
    }
    public function actionUserStatus(){
        $isLogin = !\Yii::$app->user->isGuest;
        $username = $isLogin?\Yii::$app->user->identity->username:'';
        return json_encode(['isLogin'=>$isLogin,'username'=>$username]);
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
        $content = GoodsIntro::findOne(['goods_id'=>$goods_id]);
//        var_dump($content->content);die;
//        var_dump($gallerys);die;
        return $this->render('intro',['intro'=>$intro,'gallerys'=>$gallerys,'good'=>$good,'content'=>$content]);
    }
//    public function actionDe(){
//        $redis = new \Redis();
//        $redis->connect('127.0.0.1');
//       $redis->delete('goods-category');
//    }
}
