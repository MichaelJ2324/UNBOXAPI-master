<?php

namespace Versions;

use \UNBOXAPI\Box\Module;

class Version extends Module {

    protected static $_canisters = array(
        'Applications',
        'Apis',
        'Entrypoints',
        'Parameters'
    );

    public $version;
    public $related_module;
    public $related_id;

}