<?php

return [
    'filesystem' => [
        'path' => realpath(sprintf('%s/../../../public/files', __DIR__)),
        'tmp_path' => realpath(sprintf('%s/../../../var/tmp', __DIR__)),
        'rel_path' => '/files',
    ],
];