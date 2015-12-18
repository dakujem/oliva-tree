[Oliva Tree](docs.md) > Building a tree

## Building a tree

A tree can be built by adding child nodes to their parent recursively.

```php
$node = new SimpleNode('the root');
$child = $node->addChild(new SimpleNode('first child of one'));
$child->addChild(...);
```
However, this approach is inconvenient for building trees from data that already exists in some structure or a storage.

Oliva Tree library provides "builders" for this purpose. A "builder" is any object implementing the `ITreeBuilder` interface.
The `DataTree` class is a tree that also encapsulates a builder and povides



### Building trees from linear data structures
You can build trees from linear data structures, such as database results, using `DataTree` class and tree builders. You can also build trees from data already in tree structure.




----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`Node` | `Oliva\Utils\Tree\Node\Node` | [src/Node/Node.php](../src/Node/Node.php) |[Nodes](nodes.md)|
|`SimpleNode` | `Oliva\Utils\Tree\Node\SimpleNode` | [src/Node/SimpleNode.php](../src/Node/SimpleNode.php) |[Nodes](nodes.md)|
|`NodeBase` | `Oliva\Utils\Tree\Node\NodeBase` | [src/Node/NodeBase.php](../src/Node/NodeBase.php) |[Nodes](nodes.md)|
|`DataTree` | `Oliva\Utils\Tree\DataTree` | [src/DataTree.php](../src/DataTree.php) |[Trees](trees.md)|

