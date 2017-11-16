<?php
namespace frontend\controllers;
use frontend\models\Address;
use frontend\models\ArticleCategory;
use yii\filters\AccessControl;
use yii\web\Controller;

class AddressController extends Controller{
    public $enableCsrfValidation = false;
    /**
     * 展示列表
     */
    public function actionIndex(){
//        $article_categorys = ArticleCategory::find()->all();
        $members = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])/*->orderBy(['status'=>'ASC'])*/->all();
//        var_dump($members);die;
        return $this->render('address',['members'=>$members/*,'article_categorys'=>$article_categorys*/]);
    }
    /**
     * 添加地址
     */
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new Address();
        if ($request->isPost){
            $address = $request->post();
            $model->name = $address['name'];
            $model->province = $address['cmbProvince'];
            $model->city = $address['cmbCity'];
            $model->county = $address['cmbArea'];
            $model->address = $address['address'];
            $model->phone = $address['phone'];
            if (empty($address['status'])){
                $model->status = 0;
            }else{
                $model->status = 1;
            }
            $model->member_id = \Yii::$app->user->identity->id;
            if ($model->validate()){
                if ($model->save()){
                    echo 'success';
                }else{
                    echo 'false';
                };
            }
        }
    }
    /**
     * 修改地址
     */
    public function actionEdit(){
        $id = $_GET['id'];
        $models = Address::findOne(['id'=>$id]);
        $members = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
        return  $this->render('address',['models'=>$models,'members'=>$members]);
    }
    /**
     * 修改
     */
    public function actionUpdate(){
//        var_dump($_POST);die;
        $id = $_POST['id'];
        $models = Address::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $form = $request->post();
            $models->name = $form['name'];
            $models->province = $form['cmbProvince'];
            $models->city = $form['cmbCity'];
            $models->county = $form['cmbArea'];
            $models->address = $form['address'];
            $models->phone = $form['phone'];
            if (empty($form['status'])){
                $models->status = 0;
            }else{
                $models->status = 1;
            }
            $models->member_id = \Yii::$app->user->identity->id;
            if ($models->validate()){
                if($models->save()){
                    echo 'success';
                }else{
                    echo 'false';
                }
            }else{
                var_dump($models->getErrors());exit;
            }
        }
    }
    /**
     * 删除地址
     */
    public function actionDelete($id){
        $address = Address::findOne(['id'=>$id]);
        if ($address->delete()){
            echo 'success';
        }else{
            echo 'false';
        }
    }
    /**
     * 验证地址
     */
    public function actionCheckAddress(){
        $address = $_POST['address'];
        $phone = $_POST['phone'];
//        var_dump($_POST);die;
        $member = Address::findOne(['address'=>$address]);
//        var_dump($member);die;
        if (!$member){
            return 'true';
        }else{
            if ($member->phone == $phone){
                return 'false';
            }else{
                return 'true';
            }
        }
    }
    /**
     * 修改默认地址
     */
    public function actionStatus($id){
        $address = Address::findOne(['id'=>$id]);
        if ($address->status ==1){
            $address->status = 0;
            if ($address->save()){
                return $this->redirect(['address/index']);
            }
        }else{
            Address::updateAll(['status'=>'0'],'id'!=$id);
            $address->status = 1;
            if ($address->save()){
                return $this->redirect(['address/index']);
            }
        }
    }
    /**
     * 配置
     */
//配置
 public function behaviors()
 {
     return [
         'acf'=>[ //简单存取过滤器 简单的权限控制
             'class'=>AccessControl::className(),
             'only'=>['index'],
             'rules'=>[
                 [ //允许登录用户访问  ?未登录  @已登录
                     'allow'=>true,//允许
                     'actions'=>['index'], //操作
                     'roles'=>['@'], //角色
                 ],
//                 [//允许未登录用户访问
//                     'allow'=>true,
//                     'actions'=>['login','index'],
//                     'roles'=>['?']
//                 ],
             ]

         ]

     ];
 }
}
