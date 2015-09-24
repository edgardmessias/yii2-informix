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

    public $schemaMap = [
        'informix'   => 'edgardmessias\db\informix\Schema', // Informix
    ];
    
    protected function initConnection()
    {
        if (!isset($this->attributes[PDO::ATTR_CASE])) {
            $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        }
        
        parent::initConnection();
    }
    
    public function beginTransaction($isolationLevel = null)
    {
        $transaction = parent::beginTransaction(null);
        
        if ($isolationLevel !== null) {
            $transaction->setIsolationLevel($isolationLevel);
        }
        
        return $transaction;
    }
}
