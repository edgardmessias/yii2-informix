<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class QueryBuilderTest extends \yiiunit\framework\db\QueryBuilderTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
}
