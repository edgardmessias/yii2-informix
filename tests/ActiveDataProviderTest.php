<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class ActiveDataProviderTest extends \yiiunit\framework\data\ActiveDataProviderTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
}
