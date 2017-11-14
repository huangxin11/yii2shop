<?php
/*$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'content')->textarea();
\yii\bootstrap\ActiveForm::end();*/
?>

<html>
<div class="container">
    <h2><?=$article->name?></h2>
    <div class="row">
        <div class='col-lg-8'>文章分类：<?=$article->category->name?></div><div class="col-lg-2">上传时间：<?=date('Y-m-d',$article->create_time)?></div>
        <hr>
        文章内容：<br>
        <?=$model->content?>
    </div>
</div>
</html>
