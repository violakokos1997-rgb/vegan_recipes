<?php
namespace Forms;
class User extends \Forms\Form {
    protected const FIELDS = [
        'email' => ['type' => 'email'],
        'name1' => ['type' => 'string', 'optional' => TRUE],
        'name2' => ['type' => 'string', 'optional' => TRUE],
        'emailme' => ['type' => 'boolean', 'initial' => TRUE]
    ];
}
