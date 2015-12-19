[Oliva Tree](docs.md) > Nodes

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
$node->foo;            // equal to calling $node->getContents()->foo
$node->setFoo('bar');  // equal to calling $node->getContents()->setFoo('bar');
$node->getFoo();       // again, the call is forwarded to the inner object
$node->isParent();     // calling a method of the node itself
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
$node->getValue() === $myObject;              // TRUE
$node->getValue() === $$node->getContents();  // TRUE
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
For building trees from data structures, fluent tree building and more, see [Building a tree](building.md).


## Convenience methods

There is a bunch of convenience methods that make the work with Oliva trees an easy task.

Here is a snippet using some of the methods:
```php
$node = new SimpleNode('the root');
$child = $node->addChild(new SimpleNode('first child of one'));
$node->addChildren([new SimpleNode('second child of one'), new SimpleNode('third child of one')]);
$child->getParent() === $node;        // TRUE
$siblings = $child->getSiblings();
$allChildren = $node->getChildren();
$child->isFirst();                    // TRUE - first of the siblings
$child->isLast();                     // FALSE - last of the siblings

$child->getDepth();                   // returns 1 - since it only has one parent, the root (the root has depth 0)

$child->detach();                     // detach from the tree, becomes root
$child->isRoot();                     // TRUE

// implements IteratorAggregate interface
foreach($node as $key => $childNode){...}
```

Form all available methods, see the sources of [`NodeBase`](../src/Node/NodeBase.php) and its traits.


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

## Comparing nodes

To compare the data of nodes, use the `NodeComparator` class.
```php
$comparator = new NodeComparator(); // the comparator

// simple nodes
$node1 = new SimpleNode(1);
$node2 = new SimpleNode(1);
$node3 = new SimpleNode(2);

// basic comparison
$comparator->compare($node1, $node2); // TRUE
$comparator->compare($node1, $node3); // FALSE
```
`NodeComparator` offers flexible configuration options, compares whole tree branches recursively and more. See: [Comparing nodes](comparing.md)


## Using `Node` with other data types

Using an associative `array` as the data, one can access the members of the array like it was a `stdClass` object:
```php
$node = new Node(['title' => NULL]);
$node->title = 'item one';
$child = $node->addChild(new Node(['title' => 'first child of one']));
```

It is also possible to use any scalar type as the data.
```php
$node = new Node('foo');
$node2 = new Node(2);
$node->getContents() === 'foo'; // TRUE
```

Obviously, arithmetic and other operators won't work on Node instances. You need to use the `$node->getContents()` method.
```php
$value = $node1->getcontents() + $node2->getContents();
```

> Note: using array as data for a `Node` class instance may be a little cumbersome (see [caveats of using `Node`](caveats.md)). The class is best fitted for using objects as the data.


----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`INode` | `Oliva\Utils\Tree\Node\INode` | [src/Node/INode.php](../src/Node/INode.php) ||
|`IDataNode` | `Oliva\Utils\Tree\Node\IDataNode` | [src/Node/IDataNode.php](../src/Node/IDataNode.php) ||
|`NodeBase` | `Oliva\Utils\Tree\Node\NodeBase` | [src/Node/NodeBase.php](../src/Node/NodeBase.php) ||
|`SimpleNode` | `Oliva\Utils\Tree\Node\SimpleNode` | [src/Node/SimpleNode.php](../src/Node/SimpleNode.php) ||
|`Node` | `Oliva\Utils\Tree\Node\Node` | [src/Node/Node.php](../src/Node/Node.php) ||
|`INodeComparator` | `Oliva\Utils\Tree\Comparator\INodeComparator` | [src/Comparator/INodeComparator.php](../src/Comparator/INodeComparator.php) |[Comparing nodes](comparing.md)|
|`NodeComparator` | `Oliva\Utils\Tree\Comparator\NodeComparator` | [src/Comparator/NodeComparator.php](../src/Comparator/NodeComparator.php) |[Comparing nodes](comparing.md)|

