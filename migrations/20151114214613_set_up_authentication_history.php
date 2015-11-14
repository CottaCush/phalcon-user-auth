<?php

use Phinx\Migration\AbstractMigration;

class SetUpAuthenticationHistory extends AbstractMigration
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
        $this->table('user_login_history')
            ->addColumn('user_id', 'integer', ['length' => 11])
            ->addColumn('login_status', 'string', ['length' => 100])
            ->addColumn('ip_address', 'string', ['length' => 100, 'null' => true])
            ->addColumn('user_agent', 'string', ['length' => 100, 'null' => true])
            ->addColumn('date_logged', 'datetime', ['null' => false])
            ->addForeignKey('user_id', 'user_credentials', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
