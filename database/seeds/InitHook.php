<?php

use app\entities\Install;
use Phinx\Seed\AbstractSeed;

class InitHook extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $table = $this->table('hooks');
        $table->insert(Install::hook())->save();
    }
}
