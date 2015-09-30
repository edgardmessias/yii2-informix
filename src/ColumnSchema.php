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
class ColumnSchema extends \yii\db\ColumnSchema
{
    /**
     * Converts the input value according to [[type]] and [[dbType]] for use in a db query.
     * If the value is null or an [[Expression]], it will not be converted.
     * @param mixed $value input value
     * @return mixed converted value. This may also be an array containing the value as the first element
     * and the PDO type as the second element.
     */
    public function dbTypecast($value)
    {
        if ($this->phpType == Schema::TYPE_BOOLEAN) {
            return ($value)? 't':'f';
        }
        
        // the default implementation does the same as casting for PHP, but it should be possible
        // to override this with annotation of explicit PDO type.
        return $this->typecast($value);
    }

    /**
     * Converts the input value according to [[phpType]] after retrieval from the database.
     * If the value is null or an [[Expression]], it will not be converted.
     * @param mixed $value input value
     * @return mixed converted value
     * @since 2.0.3
     */
    protected function typecast($value)
    {
        if ($value === '' && $this->type !== Schema::TYPE_TEXT && $this->type !== Schema::TYPE_STRING && $this->type !== Schema::TYPE_BINARY) {
            return null;
        }
        if ($value === null || gettype($value) === $this->phpType || $value instanceof \yii\db\Expression) {
            return $value;
        }
        switch ($this->phpType) {
            case 'resource':
            case 'string':
                if (is_resource($value)) {
                    return $value;
                }
                if (is_float($value)) {
                    // ensure type cast always has . as decimal separator in all locales
                    return str_replace(',', '.', (string) $value);
                }
                return (string) $value;
            case 'integer':
                return (int) $value;
            case 'boolean':
                if ($value === 'f') {
                    return false;
                } elseif ($value === 't') {
                    return true;
                }
                // treating a 0 bit value as false too
                // https://github.com/yiisoft/yii2/issues/9006
                return (bool) $value && $value !== "\0";
            case 'double':
                return (double) $value;
        }

        return $value;
    }
}
