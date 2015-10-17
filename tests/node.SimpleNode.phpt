<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */

namespace Oliva\Test\SimpleNode;

require_once __DIR__ . '/bootstrap.php';

use Tester\Assert;
use Oliva\Utils\Tree\Node\SimpleNode;

$node = new SimpleNode(1);

Assert::same(1, $node->value);
Assert::same(1, $node->getValue());

$node->setValue('foobar');
Assert::same('foobar', $node->getValue());

