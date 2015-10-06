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
    
    public function testFindAsArray()
    {
        $this->markTestSkipped();
    }
    
    public function testPopulateWithoutPk()
    {
        //CamelCase works if DELIMIDENT is enabled
        if (\yiiunit\data\ar\Customer::$db->isDelimident()) {
            parent::testPopulateWithoutPk();
            return;
        }
        
        // tests with single pk asArray
        $aggregation = Customer::find()
            ->select(['{{customer}}.[[status]]', 'SUM({{order}}.[[total]]) AS [[sumtotal]]'])
            ->joinWith('ordersPlain', false)
            ->groupBy('{{customer}}.[[status]]')
            ->orderBy('status')
            ->asArray()
            ->all();

        $expected = [
            [
                'status' => 1,
                'sumtotal' => 183,
            ],
            [
                'status' => 2,
                'sumtotal' => 0,
            ],
        ];
        $this->assertEquals($expected, $aggregation);

        // tests with composite pk asArray
        $aggregation = OrderItem::find()
            ->select(['[[order_id]]', 'SUM([[subtotal]]) AS [[subtotal]]'])
            ->joinWith('order', false)
            ->groupBy('[[order_id]]')
            ->orderBy('[[order_id]]')
            ->asArray()
            ->all();
        $expected = [
            [
                'order_id' => 1,
                'subtotal' => 70,
            ],
            [
                'order_id' => 2,
                'subtotal' => 33,
            ],
            [
                'order_id' => 3,
                'subtotal' => 40,
            ],
        ];
        $this->assertEquals($expected, $aggregation);

        // tests with composite pk with Models
        $aggregation = OrderItem::find()
            ->select(['[[order_id]]', 'SUM([[subtotal]]) AS [[subtotal]]'])
            ->joinWith('order', false)
            ->groupBy('[[order_id]]')
            ->orderBy('[[order_id]]')
            ->all();
        $this->assertCount(3, $aggregation);
        $this->assertContainsOnlyInstancesOf(OrderItem::className(), $aggregation);
        foreach ($aggregation as $item) {
            if ($item->order_id == 1) {
                $this->assertEquals(70, $item->subtotal);
            } elseif ($item->order_id == 2) {
                $this->assertEquals(33, $item->subtotal);
            } elseif ($item->order_id == 3) {
                $this->assertEquals(40, $item->subtotal);
            }
        }
    }
}
