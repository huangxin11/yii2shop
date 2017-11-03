<table class="table">
    <tr>
        <th>ID</th>
        <th>文章名称</th>
        <th>简介</th>
        <th>排序</th>
        <th>分类</th>
        <th>操作</th>
    </tr>
    <?php foreach ($articles as $article):?>
        <tr>
            <td><?=$article->id?></td>
            <td><?=$article->name?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->sort?></td>
            <td><?=$article->category->name?></td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['article/update','id'=>$article->id])?>" class="btn btn-link">修改</a>
                <a href="<?= \yii\helpers\Url::to(['article/view','id'=>$article->id])?>" class="btn btn-link">展示内容</a>
                <a href="javascript:;" class="del btn btn-link">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="6">
            <a href="<?= \yii\helpers\Url::to(['article/add'])?>" class="btn btn-link">添加</a>
        </td>
    </tr>
</table>

<script>
    <?php $this->beginBlock('myjs');?>
    $("table").on('click','.del',function () {
        var id = $(this).closest("tr").find("td:first").text();
        var that = this;
        if (confirm('是否删除')){
            $.post('delete',{id:id},function (data) {
                if (data == 1){
                    $(that).closest("tr").remove();
                }
            });
        }

    });
    <?php $this->endBlock();?>
</script>
<?php $this->registerJs($this->blocks['myjs']);?>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
]);
