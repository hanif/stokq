<?php

return [
    'doctrine_orm' => [
        'entity_class' => 'Stokq\Entity\User',
        'identity_field' => 'email',
        'credentials_field' => 'password',
        'identity_input' => 'email',
        'credentials_input' => 'password',
    ],
];