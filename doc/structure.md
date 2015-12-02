# Proposed changes in package structure

## Namespaces

*Option 1*
```
Oliva
|
+--- Node
|
+--- NodeComparator
|
+--- Tree
|
+--- TreeBuilder
|
+--- TreeIterator / NodeIterator
```

*Option 2*
```
Oliva
|
+--- Node
|    |
|    +--- Comparator
|    |
|    +--- Iterator
|
+--- Tree
     |
     +--- Builder
```

*Option 3*
No namespaces within the package. Namespaces ~may~ will not follow the folder structure.
```
Oliva
|
+--- Tree
```


## Class name changes

Is `TreeIterator` really an `ITree` iterator or is it an `INode` iterator?
=> rename to `INodeIterator`
=> adjust namespaces accordingly


## Interfaces

Decide whether to use interface names in form of `INode` or `NodeInterface`.
The latter may be better when not using namespaces within the package.

