<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace edgardmessias\db\informix;

/**
 * @author Edgard Messias <edgardmessias@gmail.com>
 * @since 1.0
 */
class Schema extends \yii\db\Schema
{
    
    private $tabids = [];

    /**
     * Resolves the table name and schema name (if any).
     * @param TableSchema $table the table metadata object
     * @param string $name the table name
     */
    protected function resolveTableNames($table, $name)
    {
        $parts = explode('.', str_replace('"', '', $name));
        if (isset($parts[1])) {
            $table->schemaName = $parts[0];
            $table->name = $parts[1];
        } else {
            $table->schemaName = $this->defaultSchema;
            $table->name = $name;
        }
        $table->fullName = $table->schemaName !== $this->defaultSchema ? $table->schemaName . '.' . $table->name : $table->name;
    }
    
    /**
     * Quotes a simple table name for use in a query.
     * A simple table name should contain the table name only without any schema prefix.
     * If the table name is already quoted, this method will do nothing.
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public function quoteSimpleTableName($name)
    {
        return trim($name, "\"'`");
    }

    /**
     * Quotes a simple column name for use in a query.
     * A simple column name should contain the column name only without any prefix.
     * If the column name is already quoted or is the asterisk character '*', this method will do nothing.
     * @param string $name column name
     * @return string the properly quoted column name
     */
    public function quoteSimpleColumnName($name)
    {
        return trim($name, "\"'`");
    }

    
    /**
     * Loads the metadata for the specified table.
     * @param string $name table name
     * @return \yii\db\TableSchema|null driver dependent table metadata. Null if the table does not exist.
     */
    protected function loadTableSchema($name)
    {
        $table = new \yii\db\TableSchema;
        $this->resolveTableNames($table, $name);
        if (!$this->findColumns($table)) {
            return null;
        }
        $this->findConstraints($table);
        return $table;
    }
    

    /**
     * Collects the table column metadata.
     *
     * @param TableSchema $table the table metadata
     * @return boolean whether the table exists in the database
     */
    protected function findColumns($table)
    {
        $sql = <<<SQL
SELECT syscolumns.colname,
       syscolumns.colmin,
       syscolumns.colmax,
       syscolumns.coltype,
       syscolumns.extended_id,
       NOT(coltype>255) AS allownull,
       syscolumns.collength,
       sysdefaults.type AS deftype,
       sysdefaults.default AS defvalue
FROM systables
  INNER JOIN syscolumns ON syscolumns.tabid = systables.tabid
  LEFT JOIN sysdefaults ON sysdefaults.tabid = syscolumns.tabid AND sysdefaults.colno = syscolumns.colno
WHERE systables.tabid >= 100
AND   systables.tabname = :tableName
ORDER BY syscolumns.colno
SQL;

        try {
            $columns = $this->db->createCommand($sql, [
                ':tableName' => $table->name,
            ])->queryAll();
        } catch (\Exception $e) {
            return false;
        }
        if (empty($columns)) {
            return false;
        }

        $columnsTypes = [
            0  => 'CHAR',
            1  => 'SMALLINT',
            2  => 'INTEGER',
            3  => 'FLOAT',
            4  => 'SMALLFLOAT',
            5  => 'DECIMAL',
            6  => 'SERIAL',
            7  => 'DATE',
            8  => 'MONEY',
            9  => 'NULL',
            10 => 'DATETIME',
            11 => 'BYTE',
            12 => 'TEXT',
            13 => 'VARCHAR',
            14 => 'INTERVAL',
            15 => 'NCHAR',
            16 => 'NVARCHAR',
            17 => 'INT8',
            18 => 'SERIAL8',
            19 => 'SET',
            20 => 'MULTISET',
            21 => 'LIST',
            22 => 'ROW',
            23 => 'COLLECTION',
            24 => 'ROWREF',
            40 => 'VARIABLELENGTH',
            41 => 'FIXEDLENGTH',
            42 => 'REFSER8',
            52 => 'BIGINT',
            53 => 'BIGINT',
        ];
        foreach ($columns as $column) {
            if ($this->db->slavePdo->getAttribute(\PDO::ATTR_CASE) === \PDO::CASE_UPPER) {
                $column = array_change_key_case($column, CASE_LOWER);
            }
            $coltypebase = (int) $column['coltype'];
            $coltypereal = $coltypebase % 256;
            if (array_key_exists($coltypereal, $columnsTypes)) {
                $column['type'] = $columnsTypes[$coltypereal];
                $extended_id = (int) $column['extended_id'];
                switch ($coltypereal) {
                    case 5:
                    case 8:
                        $column['collength'] = floor($column['collength'] / 256) . ',' . $column['collength'] % 256;
                        break;
                    case 14:
                    case 10:
                        $datetimeLength = '';
                        $datetimeTypes = [
                            0  => 'YEAR',
                            2  => 'MONTH',
                            4  => 'DAY',
                            6  => 'HOUR',
                            8  => 'MINUTE',
                            10 => 'SECOND',
                            11 => 'FRACTION',
                            12 => 'FRACTION',
                            13 => 'FRACTION',
                            14 => 'FRACTION',
                            15 => 'FRACTION',
                        ];
                        $largestQualifier = floor(($column['collength'] % 256) / 16);
                        $smallestQualifier = $column['collength'] % 16;
                        //Largest Qualifier
                        $datetimeLength .= (isset($datetimeTypes[$largestQualifier])) ? $datetimeTypes[$largestQualifier] : 'UNKNOWN';
                        if ($coltypereal == 14) {
                            //INTERVAL
                            $datetimeLength .= '(' . (floor($column['collength'] / 256) + floor(($column['collength'] % 256) / 16) - ($column['collength'] % 16) ) . ')';
                        } else {
                            //DATETIME
                            if (in_array($largestQualifier, [11, 12, 13, 14, 15])) {
                                $datetimeLength .= '(' . ($largestQualifier - 10) . ')';
                            }
                        }
                        $datetimeLength .= ' TO ';
                        //Smallest Qualifier
                        $datetimeLength .= (isset($datetimeTypes[$smallestQualifier])) ? $datetimeTypes[$smallestQualifier] : 'UNKNOWN';
                        if (in_array($largestQualifier, [11, 12, 13, 14, 15])) {
                            $datetimeLength .= '(' . ($largestQualifier - 10) . ')';
                        }
                        $column['collength'] = $datetimeLength;
                        break;
                    case 40:
                        if ($extended_id == 1) {
                            $column['type'] = 'LVARCHAR';
                        } else {
                            $column['type'] = 'UDTVAR';
                        }
                        break;
                    case 41:
                        switch ($extended_id) {
                            case 5:
                                $column['type'] = 'BOOLEAN';
                                break;
                            case 10:
                                $column['type'] = 'BLOB';
                                break;
                            case 11:
                                $column['type'] = 'CLOB';
                                break;
                            default :
                                $column['type'] = 'UDTFIXED';
                                break;
                        }
                        break;
                }
            } else {
                $column['type'] = 'UNKNOWN';
            }
            //http://publib.boulder.ibm.com/infocenter/idshelp/v10/index.jsp?topic=/com.ibm.sqlr.doc/sqlrmst48.htm
            switch ($column['deftype']) {
                case 'C':
                    $column['defvalue'] = 'CURRENT';
                    break;
                case 'N':
                    $column['defvalue'] = 'NULL';
                    break;
                case 'S':
                    $column['defvalue'] = 'DBSERVERNAME';
                    break;
                case 'T':
                    $column['defvalue'] = 'TODAY';
                    break;
                case 'U':
                    $column['defvalue'] = 'USER';
                    break;
                case 'L':
                    //CHAR, NCHAR, VARCHAR, NVARCHAR, LVARCHAR, VARIABLELENGTH, FIXEDLENGTH
                    if (in_array($coltypereal, [0, 15, 16, 13, 40, 41])) {
                        $explod = explode(chr(0), $column['defvalue']);
                        $column['defvalue'] = isset($explod[0]) ? $explod[0] : '';
                    } else {
                        $explod = explode(' ', $column['defvalue']);
                        $column['defvalue'] = isset($explod[1]) ? $explod[1] : '';
                        if (in_array($coltypereal, [3, 5, 8])) {
                            $column['defvalue'] = (string) (float) $column['defvalue'];
                        }
                    }
                    //Literal value
                    break;
            }
            $c = $this->createColumn($column);
            $table->columns[$c->name] = $c;
        }
        return true;
    }

    /**
     * Creates a table column.
     *
     * @param array $column column metadata
     * @return ColumnSchema normalized column metadata
     */
    protected function createColumn($column)
    {
        $c = $this->createColumnSchema();
        $c->name = $column['colname'];
        $c->allowNull = (boolean) $column['allownull'];
        $c->isPrimaryKey = false;
        $c->autoIncrement = stripos($column['type'], 'serial') !== false;
        if (preg_match('/(char|numeric|decimal|money)/i', $column['type'])) {
            $column['type'] .= '(' . $column['collength'] . ')';
        } elseif (preg_match('/(datetime|interval)/i', $column['type'])) {
            $column['type'] .= ' ' . $column['collength'];
        }
        
        $c->dbType = $column['type'];
        $c->defaultValue = $column['defvalue'];
        return $c;
    }

    protected function getColumnsNumber($tabid)
    {
        if (isset($this->tabids[$tabid])) {
            return $this->tabids[$tabid];
        }
        $qry = "SELECT colno, TRIM(colname) as colname FROM syscolumns where tabid = :tabid ORDER BY colno ";
        $command = $this->db->createCommand($qry, [':tabid' => $tabid]);
        
        $columns = [];
        foreach ($command->queryAll() as $row) {
            if ($this->db->slavePdo->getAttribute(\PDO::ATTR_CASE) === \PDO::CASE_UPPER) {
                $row = array_change_key_case($row, CASE_LOWER);
            }
            $columns[$row['colno']] = $row['colname'];
        }
        $this->tabids[$tabid] = $columns;
        return $columns;
    }

    /**
     * Collects the primary and foreign key column details for the given table.
     * @param CInformixTableSchema $table the table metadata
     */
    protected function findConstraints($table)
    {
        $sql = <<<EOD
SELECT sysconstraints.constrtype, sysconstraints.idxname
FROM systables
  INNER JOIN sysconstraints ON sysconstraints.tabid = systables.tabid
WHERE systables.tabname = :table;
EOD;
        $command = $this->db->createCommand($sql, [':table' => $table->name]);

        foreach ($command->queryAll() as $row) {
            if ($this->db->slavePdo->getAttribute(\PDO::ATTR_CASE) === \PDO::CASE_UPPER) {
                $row = array_change_key_case($row, CASE_LOWER);
            }
            if ($row['constrtype'] === 'P') { // primary key
                $this->findPrimaryKey($table, $row['idxname']);
            } elseif ($row['constrtype'] === 'R') { // foreign key
                $this->findForeignKey($table, $row['idxname']);
            }
        }
    }

    /**
     * Collects primary key information.
     * @param CInformixTableSchema $table the table metadata
     * @param string $indice Informix primary key index name
     */
    protected function findPrimaryKey($table, $indice)
    {
        $sql = <<<EOD
SELECT tabid,
       part1,
       part2,
       part3,
       part4,
       part5,
       part6,
       part7,
       part8,
       part9,
       part10,
       part11,
       part12,
       part13,
       part14,
       part15,
       part16
FROM sysindexes
WHERE idxname = :indice;
EOD;

        $command = $this->db->createCommand($sql, [':indice' => $indice]);
        foreach ($command->queryAll() as $row) {
            if ($this->db->slavePdo->getAttribute(\PDO::ATTR_CASE) === \PDO::CASE_UPPER) {
                $row = array_change_key_case($row, CASE_LOWER);
            }

            $columns = $this->getColumnsNumber($row['tabid']);
            for ($x = 1; $x < 16; $x++) {
                $colno = (isset($row["part{$x}"])) ? abs($row["part{$x}"]) : 0;
                if ($colno == 0) {
                    continue;
                }
                $colname = $columns[$colno];
                if (isset($table->columns[$colname])) {
                    $table->columns[$colname]->isPrimaryKey = true;
                    if ($table->primaryKey === null) {
                        $table->primaryKey = $colname;
                    } elseif (is_string($table->primaryKey))
                        $table->primaryKey = [$table->primaryKey, $colname];
                    else {
                        $table->primaryKey[] = $colname;
                    }
                }
            }
        }
        /* @var $c CInformixColumnSchema */
        foreach ($table->columns as $c) {
            if ($c->autoIncrement && $c->isPrimaryKey) {
                $table->sequenceName = $c->rawName;
                break;
            }
        }
    }

    /**
     * Collects foreign key information.
     * @param CInformixTableSchema $table the table metadata
     * @param string $indice Informix foreign key index name
     */
    protected function findForeignKey($table, $indice)
    {
        $sql = <<<EOD
SELECT sysindexes.tabid AS basetabid,
       sysindexes.part1 AS basepart1,
       sysindexes.part2 as basepart2,
       sysindexes.part3 as basepart3,
       sysindexes.part4 as basepart4,
       sysindexes.part5 as basepart5,
       sysindexes.part6 as basepart6,
       sysindexes.part7 as basepart7,
       sysindexes.part8 as basepart8,
       sysindexes.part9 as basepart9,
       sysindexes.part10 as basepart10,
       sysindexes.part11 as basepart11,
       sysindexes.part12 as basepart12,
       sysindexes.part13 as basepart13,
       sysindexes.part14 as basepart14,
       sysindexes.part15 as basepart15,
       sysindexes.part16 as basepart16,
       stf.tabid AS reftabid,
       TRIM(stf.tabname) AS reftabname,
       TRIM(stf.owner) AS refowner,
       sif.part1 as refpart1,
       sif.part2 as refpart2,
       sif.part3 as refpart3,
       sif.part4 as refpart4,
       sif.part5 as refpart5,
       sif.part6 as refpart6,
       sif.part7 as refpart7,
       sif.part8 as refpart8,
       sif.part9 as refpart9,
       sif.part10 as refpart10,
       sif.part11 as refpart11,
       sif.part12 as refpart12,
       sif.part13 as refpart13,
       sif.part14 as refpart14,
       sif.part15 as refpart15,
       sif.part16 as refpart16
FROM sysindexes
  INNER JOIN sysconstraints ON sysconstraints.idxname = sysindexes.idxname
  INNER JOIN sysreferences ON sysreferences.constrid = sysconstraints.constrid
  INNER JOIN systables AS stf ON stf.tabid = sysreferences.ptabid
  INNER JOIN sysconstraints AS scf ON scf.constrid = sysreferences. 'primary'
  INNER JOIN sysindexes AS sif ON sif.idxname = scf.idxname
WHERE sysindexes.idxname = :indice;
EOD;

        $command = $this->db->createCommand($sql, [':indice' => $indice]);
        foreach ($command->queryAll() as $row) {
            if ($this->db->slavePdo->getAttribute(\PDO::ATTR_CASE) === \PDO::CASE_UPPER) {
                $row = array_change_key_case($row, CASE_LOWER);
            }

            $columnsbase = $this->getColumnsNumber($row['basetabid']);
            $columnsrefer = $this->getColumnsNumber($row['reftabid']);
            for ($x = 1; $x < 16; $x++) {
                $colnobase = (isset($row["basepart{$x}"])) ? abs($row["basepart{$x}"]) : 0;
                if ($colnobase == 0) {
                    continue;
                }
                $colnamebase = $columnsbase[$colnobase];
                $colnoref = (isset($row["refpart{$x}"])) ? abs($row["refpart{$x}"]) : 0;
                if ($colnoref == 0) {
                    continue;
                }
                $colnameref = $columnsrefer[$colnoref];
                if (isset($table->columns[$colnamebase])) {
                    $table->columns[$colnamebase]->isForeignKey = true;
                }
                $table->foreignKeys[$colnamebase] = [$row['reftabname'], $colnameref];
            }
        }
    }
}
