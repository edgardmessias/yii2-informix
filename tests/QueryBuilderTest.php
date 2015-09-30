<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class QueryBuilderTest extends \yiiunit\framework\db\QueryBuilderTest
{

    use DatabaseTestTrait;
    use \yii\db\SchemaBuilderTrait;

    protected $driverName = 'informix';

    /**
     * @throws \Exception
     * @return \edgardmessias\db\informix\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return new \edgardmessias\db\informix\QueryBuilder($this->getConnection(true, false));
    }

    /**
     * adjust dbms specific escaping
     * @param $sql
     * @return mixed
     */
    protected function replaceQuotes($sql)
    {
        return str_replace('`', '', $sql);
    }
}
