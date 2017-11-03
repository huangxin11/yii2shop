<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class ArticleController extends Controller{
    /**
     * 文章展示
     */
    public function actionIndex(){
        $query = Article::find();
        $pager = new Pagination();
        //总页数
        $pager->totalCount = $query->where(['>','status',0])->count();
        //每页
        $pager->pageSize = 2;
        $articles = $query->where(['>','status',0])->limit($pager->pageSize)->offset($pager->offset)->all();
        return $this->render('index',['articles'=>$articles,'pager'=>$pager]);

    }
    /**
     * 添加
     */
    public function actionAdd(){
    $model = new Article();
    $model1 = new ArticleDetail();
    $request = \Yii::$app->request;
    if ($request->isPost){
        $model->load($request->post());
        $model1->load($request->post());
//        var_dump($model);die;
        if ($model->validate()){
            $model->create_time = time();
            $model->save(0);
            $id = \Yii::$app->db->getLastInsertID();
                $model1->article_id = $id;
                $model1->save();
        }else{
            var_dump($model->getErrors());
        }

    }
    $categorys = ArticleCategory::find()->where(['>','status',0])->all();
    $categorys = ArrayHelper::map($categorys,'id','name');
    return $this->render('add',['model'=>$model,'model1'=>$model1,'categorys'=>$categorys]);
    }
    /**
     * 修改
     */
    public function actionUpdate($id){
        $model = Article::findOne(['id'=>$id]);
        $model1=ArticleDetail::findOne(['article_id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            $model1->load($request->post());
            if ($model->validate()){
                $model->save(0);
                $model1->save();
            }else{
                var_dump($model->getErrors());
            }
        }
        $categorys = ArticleCategory::find()->where(['>','status',0])->all();
        $categorys = ArrayHelper::map($categorys,'id','name');
        return $this->render('add',['model'=>$model,'model1'=>$model1,'categorys'=>$categorys]);
    }
    /**
     * 删除
     */
    public function actionDelete(){
        $id = $_POST['id'];
        $article = Article::findOne(['id'=>$id]);
        $article->status = -1;
        if ($article->save(0)){
            echo 1;
        }
    }
    /**
     * 展示文章内容
     *
     */
    public function actionView(){
        $id=$_GET['id'];
        $model =ArticleDetail::findOne(['article_id'=>$id]);
        $article = Article::findOne(['id'=>$id]);
        return $this->render('view',['model'=>$model,'article'=>$article]);
    }
}
