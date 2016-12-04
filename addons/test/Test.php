<?php
namespace addons\test;

use Qsnh\think\Addons\Addons;

class Test extends Addons
{

    protected $_config = [
        [
            'title' => '插件名',
            'name'  => 'name',
            'type'  => 'text',
            'default' => '我是Test',
        ],
        [
            'title'   => '是否开启',
            'name'    => 'is_open',
            'type'    => 'radio',
            'default' => -1,
            'options' => [
                [
                    'title' => '开启',
                    'value' => 1,
                ],
                [
                    'title' => '关闭',
                    'value' => -1,
                ]
            ]
        ],
    ];

    public function up()
    {

    }

    public function down()
    {

    }

    public function upgrade()
    {

    }

}