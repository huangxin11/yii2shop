<?php
namespace backend\controllers;
use backend\models\EditPasswordForm;
use backend\models\LoginForm;
use backend\models\RoleForm;
use backend\models\User;
use frontend\filters\RbacFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;

class UserController extends Controller{
    /**
     * 列表
     */
    public function actionIndex(){
        $users = User::find()->all();
        return $this->render('index',['users'=>$users]);
    }
    /**
     * 添加
     */
    public function actionAdd(){
        $model = new User();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->created_at = time();
                $model->save();
                foreach ($model->roles as $role){
                    $role = \Yii::$app->authManager->getRole($role);
                    \Yii::$app->authManager->assign($role,$model->id);
                }
                \Yii::$app->session->setFlash('success','添加成功!');
                return $this->redirect(['index']);
            }else{
             var_dump($model->getErrors());
            }
        }else{
           $roles =  \Yii::$app->authManager->getRoles();
            $roles = ArrayHelper::map($roles,'name','name');
            return $this->render('add',['model'=>$model,'roles'=>$roles]);
        }
    }
    /**
     * 修改
     */
    public function actionUpdate($id){
        $auth = \Yii::$app->authManager;
        $model = User::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->updated_at = time();
                $model->save();
                $auth->revokeAll($id);
                if ($model->roles){
                    foreach ($model->roles as $role){
                        $role = $auth->getRole($role);
                        $auth->assign($role,$id);
                    }
                }
                \Yii::$app->session->setFlash('success','修改成功!');
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
            }
        }else{
            $model->roles = ArrayHelper::map($auth->getRolesByUser($model->getId()),'name','name');
            $roles = ArrayHelper::map($auth->getRoles(),'name','name');
            return $this->render('add',['model'=>$model,'roles'=>$roles]);
        }
    }
    /**
     * 删除
     */
    public function actionDelete(){
        $id = $_POST['id'];
        $user = User::findOne(['id'=>$id]);
        if ($user->delete()){
            echo 'success';
        };
    }
    /**
     * 登录
     */
    public function actionLogin(){
        $model = new LoginForm();
        $request = \Yii::$app->request;
/*        $cookie1 = \Yii::$app->request->cookies;*/
            if ($request->isPost){
                $model->load($request->post());
                if ($model->validate()){
                    if ($model->login()){
                        \Yii::$app->session->setFlash('success','登录成功!');
   /*                     if ($model->Remember_Password == 1){
                            $user = \Yii::$app->user->identity;
//                        var_dump($user->id);die;
                            $cookie = \Yii::$app->response->cookies;
                            $cookie->add(new Cookie(['name'=>'id','value'=>$user->id]));
                            $cookie->add(new Cookie(['name'=>'auth_key','value'=>$user->auth_key]));
                            $cookie->add(new Cookie(['name'=>'username','value'=>$user->username]));
                            $cookie->add(new Cookie(['name'=>'password_hash','value'=>$user->password_hash]));

                        }*/
                        return $this->redirect(['index']);
                    }else{
                        \Yii::$app->session->setFlash('success','登录失败!,用户名密码不正确');
                        return $this->redirect(['login']);
                    }            }
            }
            return $this->render('login',['model'=>$model]);

    }
    /**
     * 注销
     */
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['login']);
    }
    /**
     * 修改密码
     */
    public function actionEdit(){
        $user = \Yii::$app->user->identity;
        $request = \Yii::$app->request;
        $form = User::findOne(['id'=>$user->id]);
        $model = new EditPasswordForm();
        if ($request->isPost){
            $model->load($request->post());
          if (\Yii::$app->security->validatePassword($model->oldpassword,$user->password_hash)){
              if ($model->newpassword == $model->repassword){
                $form->password_hash = \Yii::$app->security->generatePasswordHash($model->newpassword);
                $form->updated_at = time();
                $form->auth_key = \Yii::$app->security->generateRandomString();
                $form->save();
                  \Yii::$app->session->setFlash('success','系统检测到你的身份信息已经过期，请重新登录！');
                  return $this->actionLogout();
              }else{
                  \Yii::$app->session->setFlash('success','修改失败！两次密码必须一致');
                  return $this->redirect(['edit']);
              }
          }else{
              \Yii::$app->session->setFlash('success','修改失败！旧密码错误');
              return $this->redirect(['edit']);
          }
        }else{
            return $this->render('edit',['model'=>$model]);
        }

    }
   /* //配置
    public function behaviors()
    {
        return [
            'acf'=>[ //简单存取过滤器 简单的权限控制
                'class'=>AccessControl::className(),
                'only'=>['delete','add','update','index','edit'],
                'rules'=>[
                    [ //允许登录用户访问  ?未登录  @已登录
                        'allow'=>true,//允许
                        'actions'=>['add','update','index','edit'], //操作
                        'roles'=>['@'], //角色
                    ],
                    [//允许未登录用户访问
                        'allow'=>true,
                        'actions'=>['index','login'],
                        'roles'=>['?']
                    ],
                  [
                               'allow'=>true,
                               'actions'=>['delete'],
                               'roles'=>['@'],
                               'matchCallback'=>function(){
                                   $member = \Yii::$app->user->identity;
                                   if ($member->username == 'admin'){
                                       return true;
                                   }//可以访问
                               }
                           ],
                ]

            ]

        ];
    }*/
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>\backend\filters\RbacFilter::className(),
                'except'=>['login'],
            ],
        ];
    }

}
