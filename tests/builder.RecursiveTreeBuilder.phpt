<?php

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/RecursiveTree/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Builder\RecursiveTreeBuilder;
use Oliva\Test\Scene\RecursiveTree\DefaultScene;

$parentMember = 'parent';
$idMember = 'id';

$builder = new RecursiveTreeBuilder($parentMember, $idMember);

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
Assert::same('world', $root->getChild(2)->title);
Assert::same('world second child', $root->getChild(2)->getChild(22)->title);
Assert::same('world\'s furthest leaf', $root->getChild(2)->getChild(22)->getChild(222)->getChild(2221)->title);
Assert::same('a lonely foo', $root->getChild(3)->title);
Assert::same(3, count($root->getChild(2)->getChild(22)->getChildren()));


