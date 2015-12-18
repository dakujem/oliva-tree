[Oliva Tree](docs.md) > Trees

## Trees

Trees are implementations of `ITree` interface. Class `Tree` holds the root and provides convenient methods for creating iterators easily. Most of the "tree" functionality is contained in the [node classes](nodes.md) themselves.

The `Tree` class helps create convenient iterators easily. Its descendant, `DataTree` class, serves as a container for a node builder. This way it can be reused.

To create a tree, simply provide a root node:
```php
$tree = new Tree($rootNode);
```
Iterators provide a way to **iterate through all the nodes**, **filter** them, **search** a specific node, **prune the tree** and so on. See more on iterators here: [Iterators](iterators.md)


```php
$tree = new DataTree($dataSet, $builder);
...
$tree->rebuild($anotherDataSet);
```

----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`ITree` | `Oliva\Utils\Tree\ITree` | [src/ITree.php](../src/ITree.php) ||
|`Tree` | `Oliva\Utils\Tree\Tree` | [src/Tree.php](../src/Tree.php) ||
|`IDataTree` | `Oliva\Utils\Tree\IDataTree` | [src/IDataTree.php](../src/IDataTree.php) ||
|`DataTree` | `Oliva\Utils\Tree\DataTree` | [src/DataTree.php](../src/DataTree.php) ||

