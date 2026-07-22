<?php

return [

    'backup' => [

        'source' => [

            'files' => [

                'include' => [
                    base_path('storage/app/public/orphan-attachments'),
                    base_path('storage/app/public/orphans'),
                    base_path('storage/app/public/orphans-photos'),
                    base_path('storage/app/public/thanks-messages'),
                ],

                'exclude' => [
                    base_path('storage/app/public/tmp'),
                ],

            ],

            'databases' => [
                'mysql',
            ],
        ],

        'destination' => [

            'disks' => [
                'local',
            ],
        ],

    ],

];
