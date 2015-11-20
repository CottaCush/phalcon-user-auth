<?php

use Phinx\Migration\AbstractMigration;

/**
 * Class FirstMigration
 * @author  Tega Oghenekohwo <tega@cottacush.com>
 */
class FirstMigration extends AbstractMigration
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
        if (!$this->hasTable('user_credentials')) {
            $this->table('user_credentials')
                ->addColumn('email', 'string', ['length' => 100])
                ->addColumn('password', 'string', ['length' => 100])
                ->addColumn('status', 'boolean', ['default' => 0])
                ->addColumn('created_at', 'datetime', ['null' => false])
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addIndex(array('email'), ['unique' => true])
                ->create();
        }

        if (!$this->hasTable('user_password_changes') && $this->hasTable('user_credentials')) {
            $this->table('user_password_changes')
                ->addColumn('user_id', 'integer', ['length' => 11])
                ->addColumn('password_hash', 'string', ['length' => 100])
                ->addColumn('date_changed', 'datetime', ['null' => false])
                ->addForeignKey('user_id', 'user_credentials', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        }

        if (!$this->hasTable('user_password_resets') && $this->hasTable('user_credentials')) {
            $this->table('user_password_resets')
                ->addColumn('user_id', 'integer', ['length' => 11])
                ->addColumn('token', 'string', ['length' => 200])
                ->addColumn('date_requested', 'datetime', ['null' => false])
                ->addForeignKey('user_id', 'user_credentials', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        }
    }
}
