<?php
namespace backend\controllers;
use backend\models\Menu;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class MenuController extends Controller{
    /**
     * 展示列表
     */
    public function actionIndex(){
        $menus = Menu::find()->all();
        return $this->render('index',['menus'=>$menus]);
    }
    /**
     * 添加菜单
     */
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new Menu();
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect('index');
            }else{
                var_dump($model->getErrors());
            }
        }else{
            $permission = \Yii::$app->authManager->getPermissions();
            $permissions = ArrayHelper::map($permission,'name','name');
            $permissions = array_merge(['0'=>'请选择路由'],$permissions);
            $menus = Menu::find()->where(['menu_id'=>0])->asArray()->all();
            $menus = ArrayHelper::map($menus,'id','name');
            return $this->render('add',['model'=>$model,'permissions'=>$permissions,'menus'=>$menus]);
        }
    }
    /**
     * 修改菜单
     */
    public function actionUpdate($id){
        $model = Menu::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('index');
            }else{
                var_dump($model->getErrors());
            }
        }else{
            $permission = \Yii::$app->authManager->getPermissions();
            $permissions = ArrayHelper::map($permission,'name','name');
            $permissions = array_merge(['0'=>'请选择路由'],$permissions);
            $menus = Menu::find()->asArray()->all();
            $menus = ArrayHelper::map($menus,'id','name');
            return $this->render('add',['model'=>$model,'permissions'=>$permissions,'menus'=>$menus]);
        }
    }
    /**
     * 删除菜单
     */
     public function actionDelete(){
         $id = $_POST['id'];
         $menus = Menu::findOne(['id'=>$id]);
         $children = Menu::findOne(['menu_id'=>$id]);
         if ($children){
             echo 'false';
             exit;
         }else{
             if ($menus->delete()){
                 echo 'success';
             };
         }


     }
}
