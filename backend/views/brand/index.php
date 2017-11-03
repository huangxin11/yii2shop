<table class="table">
    <tr>
        <th>ID</th>
        <th>品牌名称</th>
        <th>简介</th>
        <th>logo</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach ($brands as $brand):?>
    <tr>
        <td><?=$brand->id?></td>
        <td><?=$brand->name?></td>
        <td><?=$brand->intro?></td>
        <td><?=\yii\bootstrap\Html::img($brand->logo,['width'=>'50'])?></td>
        <td><?=$brand->sort?></td>
        <td>
            <a href="<?= \yii\helpers\Url::to(['brand/update','id'=>$brand->id])?>" class="btn btn-link">修改</a>
            <a href="javascript:;" class="del btn btn-link">删除</a>
        </td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="6">
            <a href="<?= \yii\helpers\Url::to(['brand/add'])?>" class="btn btn-link">添加</a>
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
