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
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
            return $this->render('indexpermission',['permissions'=>$permissions]);
    }
    /**
     * 添加权限
     */
    public function actionAddPermission(){
            $model = new PermissionForm();
            $model->scenario = PermissionForm::SCENARIO_Add;
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
                if ($model->validate() && $model->add()){
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['index-permission']);
                }
        }
            return $this->render('addpermission',['model'=>$model]);

    }
    /**
     * 修改权限
     */
    public function actionUpdatePermission(){
            $name = $_GET['name'];
            $auth = \Yii::$app->authManager;
            $form = new PermissionForm();
        $form->scenario = PermissionForm::SCENARIO_EDIT;
        $permission = $auth->getPermission($name);
        $name = $permission->name;
        $form->oldName = $name;
        if ($permission == null){
            throw new NotFoundHttpException('权限不存在');
        }
            $request = \Yii::$app->request;
            if ($request->isPost){
                $form->load($request->post());
                if ($form->validate() && $form->update($name)){
                    \Yii::$app->session->setFlash('success','修改成功！');
                    return $this->redirect(['index-permission']);
                }

            }

            $form->name = $permission->name;
            $form->description = $permission->description;
            return $this->render('addpermission',['model'=>$form]);
    }
    /**
     * 删除权限
     */
    public function actionDeletePermission(){
        $auth = \Yii::$app->authManager;
        $name = $_POST['name'];
//        var_dump($name);die;
        $permission = $auth->getPermission($name);
        if ($auth->remove($permission)){
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
