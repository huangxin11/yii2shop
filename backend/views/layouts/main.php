<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登录', 'url' => ['/user/login']];
    } else {
        $menuItems[] = ['label' => '文章管理', 'url' => ['/article/index'],'items'=>[
            ['label'=>'文章列表','url' => ['/article/index']],
            ['label'=>'添加文章','url' => ['/article/add']],
        ]];
        $menuItems[] = ['label' => '文章分类管理','items'=>[
            ['label'=>'文章分类列表', 'url' => ['/article-category/index']],
            ['label'=>'添加文章分类', 'url' => ['/article-category/add']],
        ]];
        $menuItems[] = ['label' => '品牌管理','items'=>[
            ['label'=>'品牌列表', 'url' => ['/brand/index']],
            ['label'=>'添加品牌', 'url' => ['/brand/add'],],
        ]];
        $menuItems[] = ['label' => '商品管理','items'=>[
            ['label'=>'商品分类列表', 'url' => ['/goods/index-category']],
            ['label'=>'添加商品分类', 'url' => ['/goods/add-category']],
            ['label'=>'添加商品', 'url' => ['/goods/add-goods']],
            ['label'=>'商品列表', 'url' => ['/goods/index-goods']],
        ]];
        $menuItems[] = ['label' => '用户管理','items'=>[
            ['label'=>'用户列表', 'url' => ['/user/index']],
            ['label'=>'添加用户', 'url' => ['/user/add']],
        ]];
        $menuItems[] = ['label' => '修改密码', 'url' => ['/user/edit']];
        $menuItems[] = '<li>'
            . Html::beginForm(['/user/logout'], 'post')
            . Html::submitButton(
                '注销 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
