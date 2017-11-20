<?php
namespace backend\controllers;
use backend\models\ArticleCategory;
use frontend\filters\RbacFilter;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\UploadedFile;

class ArticleCategoryController extends Controller{
    /**
     * 列表展示
     */
    public function actionIndex(){
        //查询器
        $query = ArticleCategory::find();
        //实例化分页类
        $pager = new Pagination();
        //总页数
        $pager->totalCount = $query->where(['>','status',0])->count();
        //每页
        $pager->pageSize = 2;
        //根据以上的条件查询数据库
        $categorys = $query->where(['status'=>1])->limit($pager->pageSize)->offset($pager->offset)->all();
        //发送数据到页面
        return $this->render('index',['categorys'=>$categorys,'pager'=>$pager]);
    }
    /**
     * 增加
     */
    public function actionAdd(){
        //实例化request
        $request = \Yii::$app->request;
        //实例化文章分类活动记录
        $model = new ArticleCategory();
        //判断是否是post方式提交数据
        if ($request->isPost){
            //post方式提交的数据使用load方法接收到活动记录，活动记录必须写配置规则才能接收到数据
            $model->load($request->post());
            //活动记录根据配置规则验证提交的数据
            if ($model->validate()){
                //因为验证码只能被验证一次，要关闭save的验证，输入0或者false
                $model->save(0);
                //保存数据成功就写提示信息
                \Yii::$app->session->setFlash('success','添加成功!');
                //跳转页面，跳转页面使用数据方式给redirect方法传递参数，当前控制器可以不写，跳转其他控制器要写控制器名，再加上方法名
                return $this->redirect(['index']);
            }else{
                //如果验证不通过就打印错误信息
                var_dump($model->getErrors());
            }
        }else{
            //如果是GET方式提交，就展示页面，实例化模型到页面
            return $this->render('add',['model'=>$model]);
        }
    }



    /**
     * 修改
     */
    public function actionUpdate($id){
        //实例化request类
        $request = \Yii::$app->request;
        //根据传递过来的的id使用findone方法查询出一条数据
        $model = ArticleCategory::findOne(['id'=>$id]);
        //判断是否是post方式提交的数据
        if ($request->isPost){
            //post方式提交数据就是用load方法接收到数据
            $model->load($request->post());
            //根据活动记录配置的规则来验证数据
            if ($model->validate()){
                //如果验证通过就保存数据库，验证码只能验证一次关闭save的验证
                $model->save(0);
                //书写提示信息
                \Yii::$app->session->setFlash('success','修改成功!');
                //跳转
                return $this->redirect(['index']);
            }else{
                //验证不通过就打印错误信息
                var_dump($model->getErrors());
            }
        }else{
            //如果是GET方式请求，就展示页面，将查询出的数据发送到页面，可以自动回显
            return $this->render('add',['model'=>$model]);
        }
    }
    /**
     * 删除
     */
    public function actionDelete(){
        //接收ajax发送的id
        $id = $_POST['id'];
        //根据id查询出要删除的数据
        $category = ArticleCategory::findOne(['id'=>$id]);
        //使用到逻辑删除，逻辑删除就是将状态值变为-1
        $category->status = -1;
        //修改数据，关闭验证
        if ($category->update(0)){
            //成功返回一个数据到页面，通过ajax回掉函数接收到
            echo 1;
        }else{
            //修改失败打印错误
            var_dump($category->getErrors());
        }
    }
        //配置RBAC必须的实例化组件
    public function behaviors()
    {
        return [
            'rbac'=>[
                //实例化类
                'class'=>\backend\filters\RbacFilter::className(),
            ],
        ];
    }


}