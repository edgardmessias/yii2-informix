<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class QueryTest extends \yiiunit\framework\db\QueryTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
}
