<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class UniqueValidatorTest extends \yiiunit\framework\validators\UniqueValidatorTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
    
    public function testValidateTargetClass() {
        $this->markTestSkipped('the pdo_informix does not support blobs in expression');
    }
}
