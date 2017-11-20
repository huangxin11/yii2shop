<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use frontend\filters\RbacFilter;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class ArticleController extends Controller{
    /**
     * 文章展示
     */
    public function actionIndex(){
        //查询器
        $query = Article::find();
        //实例化分页类
        $pager = new Pagination();
        //总页数
        $pager->totalCount = $query->where(['>','status',0])->count();
        //每页
        $pager->pageSize = 2;
        //根据条件查询数据
        $articles = $query->where(['>','status',0])->limit($pager->pageSize)->offset($pager->offset)->all();
        //将分页对象数据发送到页面
        return $this->render('index',['articles'=>$articles,'pager'=>$pager]);

    }
    /**
     * 添加
     */
    public function actionAdd(){
        //实例化文章活动记录
    $model = new Article();
    //实例化文章内容的活动记录
    $model1 = new ArticleDetail();
    //实例化request类
    $request = \Yii::$app->request;
    //判断是否是post方式提交的数据
    if ($request->isPost){
        //如果是post方式就使用两个活动记录接收数据
        $model->load($request->post());
        $model1->load($request->post());
        //验证，满足两个活动记录的规则，再保存
        if ($model->validate() && $model1->validate()){
            //添加时间
            $model->create_time = time();
            //保存数据库
            $model->save(0);
            //根据文章保存后产生的主键保存内容到文章内容表
                $model1->article_id = $model->id;
                $model1->save();
                //提示
            \Yii::$app->session->setFlash('success','添加成功!');
            //跳转
            return $this->redirect(['index']);
        }else{
            //验证不通过打印错误
            var_dump($model->getErrors());
        }
    }
    //查询文章分类的数据
    $categorys = ArticleCategory::find()->where(['>','status',0])->all();
    //将查询出的数据转换成以ID为key,nam为值的数据
    $categorys = ArrayHelper::map($categorys,'id','name');
    //发送活动记录的对象，文章分类的数据到页面
    return $this->render('add',['model'=>$model,'model1'=>$model1,'categorys'=>$categorys]);
    }
    /**
     * 修改
     */
    public function actionUpdate($id){
        //根据ID查询要修改的那条数据
        $model = Article::findOne(['id'=>$id]);
        $model1=ArticleDetail::findOne(['article_id'=>$id]);
        //实例化request类
        $request = \Yii::$app->request;
        //判断是否是post方式提交的数据
        if ($request->isPost){
            //如果是post方式提交数据就接收数据
            $model->load($request->post());
            $model1->load($request->post());
            //验证
            if ($model->validate() && $model1->validate()){
                //保存数据库
                $model->save(0);
                $model1->save();
                //提示
                \Yii::$app->session->setFlash('success','修改成功!');
                //跳转
                return $this->redirect(['index']);
            }else{
                //验证不通过打印错误
                var_dump($model->getErrors());
            }
        }
        //查询文章分类的数据
        $categorys = ArticleCategory::find()->where(['>','status',0])->all();
        //调整格式
        $categorys = ArrayHelper::map($categorys,'id','name');
        //展示添加页面
        return $this->render('add',['model'=>$model,'model1'=>$model1,'categorys'=>$categorys]);
    }
    /**
     * 删除
     */
    public function actionDelete(){
        //接收id
        $id = $_POST['id'];
        //查询数据
        $article = Article::findOne(['id'=>$id]);
        //修改状态值
        $article->status = -1;
        //保存数据库
        if ($article->save(0)){
            echo 1;
        }
    }
    /**
     * 展示文章内容
     */
    public function actionView(){
        //接收id
        $id=$_GET['id'];
        //查询该条文章的内容
        $model =ArticleDetail::findOne(['article_id'=>$id]);
        $article = Article::findOne(['id'=>$id]);
        //展示内容展示页面
        return $this->render('view',['model'=>$model,'article'=>$article]);
    }
//配置
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>\backend\filters\RbacFilter::className(),
            ],
        ];
    }
}
