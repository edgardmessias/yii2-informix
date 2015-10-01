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
class ColumnSchemaBuilder extends \yii\db\ColumnSchemaBuilder
{
    /**
     * Build full string for create the column's schema
     * @return string
     * @see http://www-01.ibm.com/support/knowledgecenter/SSGU8G_11.50.0/com.ibm.sqls.doc/ids_sqs_0111.htm%23ids_sqs_0111
     */
    public function __toString()
    {
        return
            $this->type .
            $this->buildLengthString() .
            $this->buildDefaultString() .
            $this->buildNotNullString() .
            $this->buildUniqueString() .
            $this->buildCheckString();
    }

    /**
     * Builds the default value specification for the column.
     * @return string string with default value of column.
     * @see http://www-01.ibm.com/support/knowledgecenter/SSGU8G_11.50.0/com.ibm.sqls.doc/ids_sqs_0101.htm%23ids_sqs_0101
     */
    protected function buildDefaultString()
    {
        if ($this->default === null) {
            return '';
        }

        $string = ' DEFAULT ';
        switch (gettype($this->default)) {
            case 'integer':
                $string .= (string) $this->default;
                break;
            case 'double':
                // ensure type cast always has . as decimal separator in all locales
                $string .= str_replace(',', '.', (string) $this->default);
                break;
            case 'boolean':
                $string .= $this->default ? "'t'" : "'f'";
                break;
            default:
                $string .= "'{$this->default}'";
        }

        return $string;
    }
}
