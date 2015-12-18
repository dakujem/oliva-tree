[Oliva Tree](docs.md) > Trees


## Transformations
Allows transformation of trees to 1D arrays. This can be useful for printing the tree data in linear fasion, for example to display them in tables or grids.
```php
$tree = new Tree($rootNode);
$linear = $tree->getBreadthFirst();
$linearWithDeepestFirst = $tree->getDepthFirst();
```


----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`Tree` | `Oliva\Utils\Tree\Tree` | [src/Tree.php](../src/Tree.php) ||

