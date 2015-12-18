[Oliva Tree](docs.md) > Interfaces


## Interfaces


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

`IDataTree` defines a tree that carries information not only on its root but also on a builder that created the tree structure.
This is useful when you want to rebuild the with different set of data.


### Builders

`ITreeBuilder` defines a class that produces a root tree node.
Usually this means building a tree structure from some user-provided data.



### Other

