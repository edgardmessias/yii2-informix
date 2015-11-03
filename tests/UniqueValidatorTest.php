<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class UniqueValidatorTest extends \yiiunit\framework\validators\UniqueValidatorTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
}
