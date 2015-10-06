<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace edgardmessias\db\informix;

use PDO;

/**
 * @author Edgard Messias <edgardmessias@gmail.com>
 * @since 1.0
 */
class Connection extends \yii\db\Connection
{

    /**
     * @var array PDO attributes (name => value) that should be set when calling [[open()]]
     * to establish a DB connection. Please refer to the
     * [PHP manual](http://www.php.net/manual/en/function.PDO-setAttribute.php) for
     * details about available attributes.
     */
    public $attributes = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => true,
    ];
    
    /**
     * @var array mapping between PDO driver names and [[Schema]] classes.
     * The keys of the array are PDO driver names while the values the corresponding
     * schema class name or configuration. Please refer to [[Yii::createObject()]] for
     * details on how to specify a configuration.
     *
     * This property is mainly used by [[getSchema()]] when fetching the database schema information.
     * You normally do not need to set this property unless you want to use your own
     * [[Schema]] class to support DBMS that is not supported by Yii.
     */
    public $schemaMap = [
        'informix'   => 'edgardmessias\db\informix\Schema', // Informix
    ];
    
    /**
     * Creates a command for execution.
     * @param string $sql the SQL statement to be executed
     * @param array $params the parameters to be bound to the SQL statement
     * @return Command the DB command
     */
    public function createCommand($sql = null, $params = [])
    {
        $command = new Command([
            'db' => $this,
            'sql' => $sql,
        ]);

        return $command->bindValues($params);
    }

    /**
     * Starts a transaction.
     * @param string|null $isolationLevel The isolation level to use for this transaction.
     * See [[Transaction::begin()]] for details.
     * @return Transaction the transaction initiated
     */
    public function beginTransaction($isolationLevel = null)
    {
        $transaction = parent::beginTransaction(null);
        
        if ($isolationLevel !== null) {
            $transaction->setIsolationLevel($isolationLevel);
        }
        
        return $transaction;
    }
    
    /**
     * The DELIMIDENT environment variable specifies that strings enclosed between double quotation ( " ) marks are delimited database identifiers
     * @see https://www-01.ibm.com/support/knowledgecenter/SSGU8G_12.1.0/com.ibm.sqlr.doc/ids_sqr_233.htm
     * @return boolean true if DELIMIDENT=y
     */
    public function isDelimident()
    {
        $matches = [];
        
        $delimident = '';
        if (preg_match('/DELIMIDENT=(\w)/i', $this->dsn, $matches)) {
            $delimident = $matches[1];
        } else {
            $delimident = getenv('DELIMIDENT');
        }
        
        return strtolower($delimident) == 'y';
    }
}
