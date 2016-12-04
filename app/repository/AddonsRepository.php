<?php
namespace app\repository;

use Qsnh\think\Repository\Repository;

class AddonsRepository extends Repository
{

    protected $table = 'wefee_addons';

    protected $fields = [
        'addons_sign',
        'addons_name',
        'addons_description',
        'addons_author',
        'addons_version',
        'addons_config',
    ];

}