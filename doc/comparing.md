[Oliva Tree](docs.md) > [Nodes](nodes.md) > Comparing nodes

## Comparing nodes

To compare the data of nodes, use the `NodeComparator` class.
```php
// the comparator
$comparator = new NodeComparator();

// simple nodes
$node1 = new SimpleNode(1);
$node2 = new SimpleNode(1);
$node3 = new SimpleNode(2);

// basic comparison
$comparator->compare($node1, $node2); // TRUE
$comparator->compare($node1, $node3); // FALSE
```

`NodeComparator` can be used to comfortably compare whole trees, by default it compares recursively.

The comparator
```php
$nodeA = new SimpleNode('A');
$nodeA
	->addLeaf('B', 0)
	->addLeaf('C', 2);
$nodeB = new SimpleNode('A');
$nodeB
	->addLeaf('B', 1)
	->addLeaf('C', 123);

// default comparator - indices have to be the same
$comparator = new NodeComparator();
$comparator->compare($nodeA, $nodeB); // FALSE because the node indices do not match

$nonIndexComparingComparator = new NodeComparator($recursive = TRUE, $strictness = NULL /* the default */, $compareIndices = FALSE);
$nonIndexComparingComparator->compare($nodeA, $nodeB); // TRUE this time, because the indices are ignored
```


`NodeComparator` can be configured to compare:
* with various strictness setup
	* can be set up to compare to equality `==` or identity `===` for different data types
	* by default, scalar data types, NULL and arrays are compared with `===` operator and objects with `==` operator

* non-recursively (recursive comparison is the default behaviour)
* child indices
	* by default, the child indices are ignored, however, the order of nodes does matter
* node class names
* using custom comparison function

> Note: The configuration is done at the time of construction, the object is immutable.


A **custom comparison function** can be provided:
```php
$comparator->callbackCompare($node1, $node3, function($nodeData1, $nodeData2) {
            if($nodeData1 > 10){
                return $nodeData1 > $nodeData2;
            }
			return $nodeData1 === $nodeData2;
		}); // FALSE
```



----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`NodeComparator` | `Oliva\Utils\Tree\Comparator\NodeComparator` | [src/Comparator/NodeComparator.php](../src/Comparator/NodeComparator.php) ||
|`INodeComparator` | `Oliva\Utils\Tree\Comparator\INodeComparator` | [src/Comparator/INodeComparator.php](../src/Comparator/INodeComparator.php) ||
|`SimpleNode` | `Oliva\Utils\Tree\Node\SimpleNode` | [src/Node/SimpleNode.php](../src/Node/SimpleNode.php) |[Nodes](nodes.md)|

