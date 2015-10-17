<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Tools;

require_once __DIR__ . '/bootstrap.php';

// this test should test the test tools :)

use Tester\Assert;
use Oliva\Test\DataWrapper;

// DataWrapper
$dw = new DataWrapper(123, 'foobar');

$dw->setParent(1);
$dw->setPosition('001');

$dw->setAttribute('size', 'XXL');

$dw->scalar = $scalar = 'scalaar';
$dw->array = $array = [1, 2, 3, 4, 5];
$dw->object = $object = (object) [10, 20, 30, 40, 50];


Assert::same(123, $dw->id);
Assert::same('foobar', $dw->title);
Assert::same('bar', $dw->foo);
Assert::same(1, $dw->parent);
Assert::same('001', $dw->position);
Assert::same('XXL', $dw->getAttribute('size'));
Assert::same('default', $dw->getAttribute('natreally', 'default'));


Assert::same($scalar, $dw->scalar);
Assert::same($array, $dw->array);
Assert::same($object, $dw->object);
