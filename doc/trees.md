# Trees

Trees are implementations of `ITree` interface. Class `Tree` holds the root and provides convenient methods for creating iterators easily. Most of the "tree" functionality is contained in the nodes themselves.

The `Tree` class helps create convenient iterators easily. Its descendant, `DataTree` class, serves as a container for a node builder. This way it can be reused.

To create a tree, simply provide a root node:
```php
$tree = new Tree($rootNode);
```
Iterators provide a way to **iterate through all the nodes**, **filter** them, **search** a specific node, **prune the tree** and so on.


```php
$tree = new DataTree($dataSet, $builder);
...
$tree->rebuild($anotherDataSet);
```
