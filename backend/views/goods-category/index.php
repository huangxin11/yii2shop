<span id="myspan" style="color: red"></span>
<table class="table">
    <tr>
        <th>ID</th>
        <th>分类名称</th>
        <th>分类简介</th>
        <th>父分类</th>
        <th>操作</th>
    </tr>
    <?php foreach ($categorys as $category):?>
    <tr>
        <td><?=$category->id?></td>
        <td><?=str_repeat('—',$category->depth*2).$category->name?></td>
        <td><?=$category->intro?></td>
        <td><?=$category->parent_id?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['update','id'=>$category->id])?>" class="btn btn-link">修改</a>
            <a href="javasrcipt:;" class="btn btn-link del">删除</a>
        </td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="5"><a href="<?=\yii\helpers\Url::to(['add'])?>" class="btn btn-link">添加</a></td>
    </tr>
</table>

<script>
    <?php $this->beginBlock('myjs');?>
            $("table").on('click','.del',function () {
            var id = $(this).closest("tr").find("td:first").text();
            var that = this;
            if (confirm('是否删除')){
                $.post('delete',{id:id},function (data) {
                    if(data == 'success'){
                        $(that).closest("tr").remove();
                        $("#myspan").html('删除成功！');
                    } else {
                        $("#myspan").html('删除失败！该分类还有下级分类');
                    }
                });
            }

            });
<?php $this->endBlock();?>
</script>
<?php $this->registerJs($this->blocks['myjs']);?>
