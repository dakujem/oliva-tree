<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test;

require_once __DIR__ . '/bootstrap.php';

use Tester\Assert;
use Oliva\Utils\Tree\Tree,
	Oliva\Utils\Tree\Node\Node;

$tree = new Tree();
$root = new Node(['id' => 1, 'title' => 'foo']);
$tree->setRoot($root);

Assert::equal(1, $tree->getRoot()->id);
