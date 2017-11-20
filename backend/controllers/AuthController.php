<?php
namespace backend\controllers;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AuthController extends Controller{
    /**
     * 展示权限
     */
    public function actionIndexPermission(){
        //实例化组件
        $auth = \Yii::$app->authManager;
        //获取所有的权限
        $permissions = $auth->getPermissions();
        //显示权限列表
            return $this->render('indexpermission',['permissions'=>$permissions]);
    }
    /**
     * 添加权限
     */
    public function actionAddPermission(){
        //实例化权限的表单模型
            $model = new PermissionForm();
            //自定义规则给该方法添加名字
            $model->scenario = PermissionForm::SCENARIO_Add;
            //实例化组件
        $request = \Yii::$app->request;
        //判断是否是post方式提交的数据
        if ($request->isPost){
            //接收数据
            $model->load($request->post());
            //验证并且判断添加方法是否成功
                if ($model->validate() && $model->add()){
                    //提示
                    \Yii::$app->session->setFlash('success','添加成功');
                    //跳转
                    return $this->redirect(['index-permission']);
                }
        }
        //展示添加权限页面
            return $this->render('addpermission',['model'=>$model]);

    }
    /**
     * 修改权限
     */
    public function actionUpdatePermission(){
        //接收权限名
            $name = $_GET['name'];
            //实例化组件
            $auth = \Yii::$app->authManager;
            //实例化权限表单模型
            $form = new PermissionForm();
        //自定义规则给该方法添加名字
        $form->scenario = PermissionForm::SCENARIO_EDIT;
        //通过权限名获取权限
        $permission = $auth->getPermission($name);
        //将权限名复制给$name
        $name = $permission->name;
        //赋值给oldName属性
        $form->oldName = $name;
        //判断是否有这个权限
        if ($permission == null){
            //展示错误，权限不存在
            throw new NotFoundHttpException('权限不存在');
        }
            $request = \Yii::$app->request;
        //判断是否post方式提交
            if ($request->isPost){
                //接收数据
                $form->load($request->post());
                //验证并且判断修改方法是否成功
                if ($form->validate() && $form->update($name)){
                    //提示
                    \Yii::$app->session->setFlash('success','修改成功！');
                    //跳转
                    return $this->redirect(['index-permission']);
                }

            }

            $form->name = $permission->name;
            $form->description = $permission->description;
            //展示修改表单
            return $this->render('addpermission',['model'=>$form]);
    }
    /**
     * 删除权限
     */
    public function actionDeletePermission(){
        $auth = \Yii::$app->authManager;
        $name = $_POST['name'];
//        var_dump($name);die;
        //获取权限
        $permission = $auth->getPermission($name);
        //删除权限
        if ($auth->remove($permission)){
            //返回值
            echo 'success';
            exit;
        };
    }


    /**
     * 展示角色
     */
    public function actionIndexRole(){
        $auth = \Yii::$app->authManager;
        $roles = $auth->getRoles();
        return $this->render('indexrole',['roles'=>$roles]);
    }
    /**
     * 添加角色
     */
    public function actionAddRole(){
        $auth = \Yii::$app->authManager;
        $request = \Yii::$app->request;
        $model = new RoleForm();
        if ($request->isPost){
          $model->load($request->post());
          if ($model->validate()){
              $role = $auth->createRole($model->name);
              $role->description = $model->description;
              $auth->add($role);
              foreach ($model->permissions as $permissionName){
                  $permission = $auth->getPermission($permissionName);
                  $auth->addChild($role,$permission);
              }
              \Yii::$app->session->setFlash('success','添加角色成功!');
              return $this->redirect('index-role');
          }
        }else{
            $permissions = $auth->getPermissions();
            $permissions = ArrayHelper::map($permissions,'name','description');
            return $this->render('addrole',['model'=>$model,'permissions'=>$permissions]);
        }
    }
    /**
     * 修改角色
     */
    public function actionUpdateRole(){
//        var_dump($_GET);die;
        $request = \Yii::$app->request;
        $form = new RoleForm();
        $name = $_GET['name'];
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);
        if ($request->isPost){
            $form->load($request->post());
            if ($form->validate()){
//                var_dump($form->permissions);die;
                $newRole = $auth->createRole($form->name);
                $newRole->description = $form->description;
                $auth->update($role->name,$newRole);
                $auth->removeChildren($newRole);
          if ($form->permissions){
                    foreach ($form->permissions as $permissionName){
                        $permission = $auth->getPermission($permissionName);
                        $auth->addChild($newRole,$permission);
                    }
                }
                \Yii::$app->session->setFlash('success','修改角色成功!');
                return $this->redirect('index-role');
            }
        }

        $form->name = $role->name;
        $form->description = $role->description;
        $permission = $auth->getChildren($role->name);
        $permission = ArrayHelper::map($permission,'name','name');
        $form->permissions = $permission;
        $permissions = $auth->getPermissions();
        $permissions = ArrayHelper::map($permissions,'name','description');
        return $this->render('addrole',['model'=>$form,'permissions'=>$permissions]);
    }
    /**
     * 删除角色
     */
    public function actionDeleteRole(){
        $name = $_POST['name'];
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);
        if ($auth->remove($role)){
            echo 'success';
            exit;
        }
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>\backend\filters\RbacFilter::className(),
        ],
        ];
    }
}
