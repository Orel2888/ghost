<?php

namespace App\Ghost\Install;

use DB;

class SetupDataDbCreator extends BaseInstall
{

    /**
     * Executing before install
     */
    public function runInstall()
    {
        parent::runInstall();
    }

    public function adminCreatorApply()
    {
        if (isset($this->settings['admin_accounts'])) {
            foreach ($this->settings['admin_accounts'] as $account) {
                DB::table('admins')->insert($account);
            }
        }
    }

    public function adminCreatorCancel()
    {
        DB::connection()
            ->getPdo()
            ->query('TRUNCATE TABLE admins');

        return true;
    }
}