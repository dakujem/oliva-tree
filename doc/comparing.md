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
`NodeComparator` can be configured to compare with various strictness (can be set up to compare to equality `==` or identity `===` for different data types), not to compare recursively, to compare child indices and much more.

A **custom comparison function** can also be provided:
```php
$comparator->callbackCompare($node1, $node3, function($nodeData1, $nodeData2) {
            if($nodeData1 > 10){
                return $nodeData1 > $nodeData2;
            }
			return $nodeData1 === $nodeData2;
		}); // FALSE
```
> **Note**: this is just a basic example, `NodeComparator` can of course compare whole branches with all their nodes  **recursively**.


----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`NodeComparator` | `Oliva\Utils\Tree\Comparator\NodeComparator` | [src/Comparator/NodeComparator.php](../src/Comparator/NodeComparator.php) ||
|`INodeComparator` | `Oliva\Utils\Tree\Comparator\INodeComparator` | [src/Comparator/INodeComparator.php](../src/Comparator/INodeComparator.php) ||
|`SimpleNode` | `Oliva\Utils\Tree\Node\SimpleNode` | [src/Node/SimpleNode.php](../src/Node/SimpleNode.php) |[Nodes](nodes.md)|

