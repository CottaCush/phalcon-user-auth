<?php

use Phinx\Migration\AbstractMigration;

class UserTypeSchema extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        if (!$this->hasTable('user_types')) {
            $this->table('user_types')
                ->addColumn('name', 'integer', ['length' => 100])
                ->addColumn('created_at', 'datetime', ['null' => false])
                ->addColumn('updated_at', 'datetime', ['null' => false])
                ->create();
        }

        if ($this->hasTable('user_credentials') && $this->hasTable('user_types')) {
            $table = $this->table('user_credentials');

            $table->addColumn('user_type_id', 'integer', ['length' => 11, 'null' => true, 'default' => null, 'comment' => "shows the user's type/role"])
                ->addForeignKey('user_type_id', 'user_types', ['id'], ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->update();
        }
    }
}
