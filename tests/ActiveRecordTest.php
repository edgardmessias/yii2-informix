<?php

namespace edgardmessias\unit\db\informix;

use yiiunit\data\ar\Type;

/**
 * @group informix
 */
class ActiveRecordTest extends \yiiunit\framework\db\ActiveRecordTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
    
    public function testCastValues()
    {
        $model = new Type();
        $model->int_col = 123;
        $model->int_col2 = 456;
        $model->smallint_col = 42;
        $model->char_col = '1337';
        $model->char_col2 = 'test';
        $model->char_col3 = 'test123';
        $model->float_col = 3.742;
        $model->float_col2 = 42.1337;
        $model->bool_col = true;
        $model->bool_col2 = false;
        $model->save(false);

        /* @var $model Type */
        $model = Type::find()->one();
        $this->assertSame(123, $model->int_col);
        $this->assertSame(456, $model->int_col2);
        $this->assertSame(42, $model->smallint_col);
        $this->assertSame('1337', trim($model->char_col));
        $this->assertSame('test', $model->char_col2);
        $this->assertSame('test123', $model->char_col3);
        $this->assertSame(3.742, $model->float_col);
        $this->assertSame(42.1337, $model->float_col2);
        $this->assertSame(true, $model->bool_col);
        $this->assertSame(false, $model->bool_col2);
    }
}
