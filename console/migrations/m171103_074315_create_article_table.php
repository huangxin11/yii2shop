<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m171103_074315_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('名称'),
            'intro'=>$this->text()->comment('简介'),
            'sort'=>$this->integer()->comment('排序'),
            'status'=>$this->integer()->comment('状态 -1删除，0隐藏，1显示'),
            'create_time'=>$this->integer()->comment('创建时间'),
            'article_category_id'=>$this->integer()->comment('所属分类'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
