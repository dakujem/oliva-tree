<?php

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/RecursiveTree/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Builder\RecursiveTreeBuilder;
use Oliva\Test\Scene\RecursiveTree\DefaultScene;

$parentMember = 'parent';
$idMember = 'id';

$builder = new RecursiveTreeBuilder($parentMember, $idMember);

//TODO: test class creating callbacks, error data handling aso...
/**/


//		0   root
//		|
//		+--  1   hello
//		|    |
//		|    +--  11
//		|    |
//		|    +--  12
//		|
//		+--  2   world
//		|    |
//		|    +--  21
//		|    |
//		|    +--  22
//		|    |     |
//		|    |     +--  221
//		|    |     |
//		|    |     +--  222
//		|    |     |      |
//		|    |     |      +--  2221
//		|    |     |
//		|    |     +--  223
//		|    |
//		|    +--  23
//		|
//		+--  3
$data = (new DefaultScene)->getData();
$root = $builder->build($data);




Assert::same('hello', $root->getChild(1)->title);


