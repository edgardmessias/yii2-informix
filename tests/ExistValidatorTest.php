<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class ExistValidatorTest extends \yiiunit\framework\validators\ExistValidatorTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
}
