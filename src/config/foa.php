<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 20:22
 */

return [
    'schema' => [
        'types' => [
            'text' => [
                'name' => 'Text',
                'casts' => 'string'
            ],
            'number' => [
                'name' => 'Number',
                'casts' => 'int'
            ],
        ]
    ],

    'upload' => [
        'thumbs' => [
            'small' => [
                'width' => '200'
            ],
            'medium' => [
                'width' => '400'
            ]
        ]
    ]
];