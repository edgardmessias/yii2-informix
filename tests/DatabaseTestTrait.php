<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group sphinx
 */
trait DatabaseTestTrait {

    public function setUp() {
        if (self::$params === null) {
            self::$params = require(__DIR__ . '/data/config.php');
        }

        parent::setUp();
    }

}
