[Oliva Tree](docs.md) > Interfaces


## Interfaces

Oliva Tree is based on interfaces, so if you don't like some of the implementations, you can always provide your own.

Oliva Tree tries to be as flexible as possible, so alongside interfaces it provides traits
to help you create your own implementations without dependency on its classes.
Most of the implemented methods are overridable as well.



### Nodes

`INode` is the interface that defines essential methods for tree structure operations.
Any structural operation can be performed using these methods (well, any I could think of).

`IDataNode` is an interface defining nodes that contain some data that can be retrieved.
This interface is used for comparisons and other operations that work with contents of nodes.


### Trees

Despite the name of the library, tree classes only serve as convenience method wrappers and are not essential.
Most of the "tree" logic is implemented in the node classes.
Therefore the interfaces are very simple.

`ITree` defines a tree with a single root.


### Builders

`ITreeBuilder` defines a class that produces a root tree node.
Usually this means building a tree structure from some user-provided data.



### Other

`INodeComparator` defines an implementation able to compare two `IDataNode` nodes.



----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`INode` | `Oliva\Utils\Tree\Node\INode` | [INode.php](../src/Node/INode.php) |[Nodes](nodes.md)|
|`IDataNode` | `Oliva\Utils\Tree\Node\IDataNode` | [IDataNode.php](../src/Node/IDataNode.php) |[Nodes](nodes.md)|
|`ITree` | `Oliva\Utils\Tree\ITree` | [ITree.php](../src/ITree.php) |[Trees](trees.md)|
|`ITreeBuilder` | `Oliva\Utils\Tree\Builder\ITreeBuilder` | [ITreeBuilder.php](../src/Builder/ITreeBuilder.php) |[Building trees](building.md)|
|`INodeComparator` | `Oliva\Utils\Tree\Comparator\INodeComparator` | [INodeComparator.php](../src/Comparator/INodeComparator.php) |[Comparing nodes](comparing.md)|

