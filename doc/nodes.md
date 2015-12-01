# Oliva Tree

Home > Nodes

## Nodes
A "node" is anything implementing the `INode` interface, providing basic methods for adding, getting and removing child nodes and parent.
A "data node" is anything implementing the `IDataNode` interface, providing the `getContents()` method.

There are two node implementations in the package:
* `SimpleNode`
	* carries a value of **any type**: `(new SimpleNode($myObject))->getContents()->myMethod()`
* `Node`
	* seamlessly wraps **any object** and adds all the node functionality to it
	* also works with any other data type (with some restrictions)
	* the difference to `SimpleNode` is that one can access the data directly (hence the seemlessness): `(new Node($myObject))->myMethod()`

Both implementations provide the same rich set of convenience methods through the use of traits (see below).


## The `Node` class

`Node` is an implementation allowing creation of tree nodes with **arbitrary objects** with seamless access to the original object's methods and members. It does also work with any other data type (see below).

Using any object:
```php
class Foo {
	public $foo;
	public function setFoo($foo){...}
	public function getFoo(){...}
}

$node = new Node(new Foo);
$node->foo; // equal to callintg $node->getContents()->foo
$node->setFoo('bar'); // equal to calling $node->getContents()->setFoo('bar');
$node->getFoo(); // again, the call is forwarded to the inner object
$node->isParent(); // calling a method of the node itself
$node->getContents();
```


## The `SimpleNode` class

A very simple node implementation where the node only carries a value accessible by:
* public member `value`
* public setter `setValue()` and public getter `getValue()`
* public setter `setContents()` and public getter `getContents()`

```php
$node = new SimpleNode($myObject);
$node->getContents()->myMethod();
$node->getValue() === $myObject; // TRUE
$node->getValue() === $$node->getContents(); // TRUE
```


## Building trees

```php
$node = new SimpleNode('the root');
$child = $node->addChild(new SimpleNode('first child of one'));
$node->addChildren([new SimpleNode('second child of one'), new SimpleNode('third child of one')]);
$node->getChild(2)->addChild(new SimpleNode('a leaf'));
$node->removeChild($node->getChildIndex($child));
$node->removeChildren(); // dump all the children
```


## Convenience methods

There is a bunch of convenience methods that make the work with Oliva trees an easy task.

Here is a snippet using some of the methods:
```php
$node = new SimpleNode('the root');
$child = $node->addChild(new SimpleNode('first child of one'));
$node->addChildren([new SimpleNode('second child of one'), new SimpleNode('third child of one')]);
$child->getParent() === $node; // TRUE
$siblings = $child->getSiblings();
$allChildren = $node->getChildren();
$child->isFirst(); // TRUE - first of the siblings
$child->isLast(); // FALSE - last of the siblings

$child->getDepth(); // 1 - since it only has one parent, the root (the root has depth 0)

$child->detach(); // detach from the tree, becomes root
$child->isRoot(); // TRUE

// implements IteratorAggregate interface
foreach($node as $key => $childNode){...}
```

Form all available methods, see the sources.


## Creating your own nodes
You can create your own nodes by implementing the `INode` inteface to work with the rest of the library. By implementing the `IDataNode` interface, you can compare nodes using node comparator.
Even easier, you can simply use *traits* and the interfaces to create your own nodes in no time.
As a third option, you can inherit from abstract `NodeBase` class.

```php
class MyNode implements INode, IDataNode {
	use BaseNodeTrait,
		FluentNodeTrait,
		DeepCloningTrait;

	public function getContents(){...}
}
```


## Using `Node` with other data types

Using an associative `array` as the data, one can access the members of the array like it was a `stdClass` object:
```php
$node = new Node(['title' => NULL]);
$node->title = 'item one';
$child = $node->addChild(new Node(['title' => 'first child of one']));
```
> Note: using array as data for a `Node` class instance is a little cumbersome (see caveats of using `Node`). The class is best fitted for using objects as the data.

**TODO: test scalar data type operations like +, - ...


----
TODO !
|Reference|Full class name|File|Docs|
|:-|:-|:-|:-|
|`INode` | `Oliva\Utils\Tree\Node\INode` | [src/Node/INode.php](../src/Node/INode.php) ||
|`IDataNode` | `Oliva\Utils\Tree\Node\IDataNode` | [src/Node/IDataNode.php](../src/Node/IDataNode.php) ||
|`Node` | `Oliva\Utils\Tree\Node\Node` | [src/Node/Node.php](../src/Node/Node.php) ||
|`SimpleNode` | `Oliva\Utils\Tree\Node\SimpleNode` | [src/Node/SimpleNode.php](../src/Node/SimpleNode.php) ||
|`NodeBase` | `Oliva\Utils\Tree\Node\NodeBase` | [src/Node/NodeBase.php](../src/Node/NodeBase.php) ||

