<form action="index-goods" method="get">
        <input type="text" name="name" style="width: 250px" placeholder="商品名">
        <input type="text" name="sn" style="width: 250px" placeholder="货号">
        <input type="text" name="minprice" style="width: 250px" placeholder="最小价格">
        <input type="text" name="maxprice" style="width: 250px" placeholder="最大价格">
        <input type="submit" value="搜索">
    </form>
<span id="myspan" style="color: red"></span>
<table class="table">
    <tr>
        <th>ID</th>
        <th>商品名称</th>
        <th>商品货号</th>
        <th>商品logo</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>操作</th>
    </tr>
    <?php foreach ($goods as $good):?>
    <tr>
        <td><?=$good->id?></td>
        <td><?=$good->name?></td>
        <td><?=$good->sn?></td>
        <td><?=\yii\bootstrap\Html::img($good->logo,['width'=>50])?></td>
        <td><?=$good->shop_price?></td>
        <td><?=$good->stock?></td>
        <td>
            <a href="javascript:;" class="btn btn-info del">删除</a>
            <a href="<?=\yii\helpers\Url::to(['update-goods','id'=>$good->id])?>" class="btn btn-default">修改</a>
            <a href="<?=\yii\helpers\Url::to(['add-logo','id'=>$good->id])?>" class="btn btn-success">相册</a>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<script>
    <?php $this->beginBlock('myjs');?>
    $("table").on('click','.del',function () {
        var id = $(this).closest("tr").find("td:first").text();
        var that = this;
        if (confirm('是否删除')){
            $.post('delete-goods',{id:id},function (data) {
                if(data == 'success'){
                    $(that).closest("tr").remove();
                    $("#myspan").html('删除成功！');
                } else {
                    $("#myspan").html('删除失败！');
                }
            });
        }

    });
    $("#keyword").click{
        alert(2);
    }
    <?php $this->endBlock();?>
</script>
<?php $this->registerJs($this->blocks['myjs']);?>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
]);
