<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once './DataWrapper.php';
require_once './FooWrapper.php';

use Tracy\Debugger;

// tester
Tester\Environment::setup();

// debugging
Debugger::$strictMode = TRUE;
Debugger::$logSeverity = E_NOTICE | E_WARNING;
Debugger::enable();


// dump shortcut
function dump($var, $return = FALSE)
{
	return Debugger::dump($var, $return);
}


function recursiveTreeData()
{
	// recursive tree with a single root
	$parentTreeData = [
		(new DataWrapper(0, 'root')),
		(new DataWrapper(1, 'hello'))->setParent(0),
		(new DataWrapper(2, 'world'))->setParent(0),
		(new DataWrapper(11, 'hello child'))->setParent(1),
		(new DataWrapper(12, 'hello second child'))->setParent(1),
		(new DataWrapper(21, 'world first child'))->setParent(2),
		(new DataWrapper(22, 'world second child'))->setParent(2),
		(new DataWrapper(23, 'world third child'))->setParent(2),
		(new DataWrapper(221, 'world second-first'))->setParent(22),
		(new DataWrapper(222, 'world second-second'))->setParent(22),
		(new DataWrapper(223, 'world second-third'))->setParent(22),
		(new DataWrapper(2221, 'world\'s furthest leaf'))->setParent(222),
		(new DataWrapper(3, 'a lonely foo'))->setParent(0),
	];
	// recursive tree with multiple roots
	$perentTreeMultiRootsData = [
		(new DataWrapper(1, 'root1')),
		(new DataWrapper(2, 'root2')),
		(new DataWrapper(3, 'foobar'))->setParent(1),
	];
	// recursive trees with implicit roots
	$perentTreeImplicitRootData1 = [
		(new DataWrapper(0, 'root')),
		(new DataWrapper(2, 'node'))->setParent(0),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	$perentTreeImplicitRootData2 = [
		(new DataWrapper('', 'root')),
		(new DataWrapper(2, 'node'))->setParent(''),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	$perentTreeImplicitRootData3 = [
		(new DataWrapper(NULL, 'root')),
		(new DataWrapper(2, 'node'))->setParent(NULL),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	$perentTreeImplicitRootData4 = [
		(new DataWrapper('0', 'root')),
		(new DataWrapper(2, 'node'))->setParent('0'),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	return [$parentTreeData, $perentTreeMultiRootsData, $perentTreeImplicitRootData1, $perentTreeImplicitRootData2, $perentTreeImplicitRootData3, $perentTreeImplicitRootData4,];
}


function prepareData()
{
	// path tree with undefined root
	$pathTreeData = [
		(new DataWrapper(1, 'hello'))->setPosition('001'),
		(new DataWrapper(11, 'hello child'))->setPosition('001001'),
		(new DataWrapper(12, 'hello second child'))->setPosition('001002'),
		(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('002002002001'),
		(new DataWrapper(2, 'world'))->setPosition('002'),
		(new DataWrapper(21, 'world first child'))->setPosition('002001'),
		(new DataWrapper(22, 'world second child'))->setPosition('002002'),
		(new DataWrapper(23, 'world third child'))->setPosition('002003'),
		(new DataWrapper(221, 'world second-first'))->setPosition('002002001'),
		(new DataWrapper(222, 'world second-second'))->setPosition('002002002'),
		(new DataWrapper(223, 'world second-third'))->setPosition('002002003'),
		(new DataWrapper(3, 'a lonely foo'))->setPosition('003'),
	];

	// path tree with two defined roots, second root specified after inserting some nodes
	$pathTreeData2 = [
		(new DataWrapper(0, 'root'))->setPosition(NULL),
		(new DataWrapper(1, 'hello'))->setPosition('001'),
		(new DataWrapper(11, 'hello child'))->setPosition('001001'),
		(new DataWrapper(12, 'hello second child'))->setPosition('001002'),
		(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('002002002001'),
		(new DataWrapper(2, 'world'))->setPosition('002'),
		(new DataWrapper(21, 'world first child'))->setPosition('002001'),
		(new DataWrapper(22, 'world second child'))->setPosition('002002'),
		(new DataWrapper(23, 'world third child'))->setPosition('002003'),
		(new DataWrapper(221, 'world second-first'))->setPosition('002002001'),
		(new DataWrapper(222, 'world second-second'))->setPosition('002002002'),
		(new DataWrapper(223, 'world second-third'))->setPosition('002002003'),
		(new DataWrapper(100, 'root2'))->setPosition(''),
		(new DataWrapper(3, 'a lonely foo'))->setPosition('003'),
	];

	return [$pathTreeData, $pathTreeData2];
}


function materializedTreeData()
{

	// materialized tree with undefined root
	$materializedTreeData = [
//		(new DataWrapper(1, 'hello'))->setPosition('001'),
//		(new DataWrapper(2, 'world'))->setPosition('002'),
//		(new DataWrapper(11, 'hello child'))->setPosition('001001'),
//		(new DataWrapper(12, 'hello second child'))->setPosition('001002'),
//		(new DataWrapper(21, 'world first child'))->setPosition('002001'),
//		(new DataWrapper(22, 'world second child'))->setPosition('002002'),
//		(new DataWrapper(23, 'world third child'))->setPosition('002003'),
//		(new DataWrapper(221, 'world second-first'))->setPosition('002002001'),
//		(new DataWrapper(222, 'world second-second'))->setPosition('002002002'),
//		(new DataWrapper(223, 'world second-third'))->setPosition('002002003'),
//		(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('002002002001'),
//		(new DataWrapper(3, 'a lonely foo'))->setPosition('003'),
	];
}
