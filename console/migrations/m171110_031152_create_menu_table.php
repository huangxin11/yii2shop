<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171110_031152_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->notNull()->comment('菜单名'),
            'menu_id'=>$this->integer()->defaultValue('0')->comment('上级菜单,0表示顶级菜单'),
            'sort'=>$this->integer()->comment('排序'),
            'url'=>$this->string()->comment('路由'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
