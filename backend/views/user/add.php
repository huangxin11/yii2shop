<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
if ($model->isNewRecord){
    echo $form->field($model,'password_hash')->passwordInput();
}

//echo $form->field($model,'auth_key',['inline'=>1])->radioList(['0'=>'否','1'=>'是']);
echo $form->field($model,'email')->textInput();
echo $form->field($model,'status',['inline'=>1])->radioList(['0'=>'禁用','10'=>'启用']);
echo $form->field($model,'roles',['inline'=>1])->checkboxList($roles);
echo \yii\bootstrap\Html::submitButton('添加',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
