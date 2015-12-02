[Oliva Tree](docs.md) > Materialized path tree

## Materialized path tree

`MaterializedPathTreeBuilder` builds trees from data that follow the Materialized Paths data model.
The position of a node in a tree is represented by a hierarchy string.

There are more variants of the Path trees, some use fixed length strings for each depth level, some use character delimited strings. The hierarchy may be represented by ID's or by sibling numbers (numerators). In many cases, the IDs or sibling numbers are encoded to reduce the path length.

And the good new is `MaterializedPathTreeBuilder` can handle them all.

Path trees with sibling numbers are useful for storing menu hierarchy or similar structures where position of a node within a sub-tree matters. In large data sets with big IDs this approach may also reduce data size (the paths string is considerably shorter).

In this example, each depth level is represented by a fixed-length string representing the position of the node in the current sub-tree (3 characters per depth level) and sibling numbers are used. This allows ordering of leafs and is useful for creating for example menu components.

> **Note**: this is a special case of Materialized Path data model, where fixed number of characters (3) are used for each level. No encoding is used.

| ID        | hierarchy    | title|
|:----------|:----------|:-------|
|1         | `001`| the first level|
|2|`001001`|first child of `001`
|3|`001002`|second child of `001`
|4|`001001001`|third level
|5|`001001002`|third level, second child
|6|`002001`|a child of a node not yet specified - this will work
|7|`002`|second first-level item
|100|`NULL`|the root - this needs not be specified, an empty root will be created automatically

Get this structure from a database as an array.
Tell the tree that you are using 'position' as the hierarchy column and there are 3 characters for each level.
Create the tree.
```php
$tree = new DataTree($data, new MaterializedPathTreeBuilder('hierarchy', 3));
```
Unless specified with NULL position (or position that is shorter than number of characters needed for each level),
an empty root node will be created to wrap the data tree. You can provide a data item with NULL position to specify your own root.

> Note: if you have more than one root, the later will override the previous one.

And now an example of a delimited hierarchy string with node position in `position` member.

| ID        | position    | title|
|:----------|:----------|:-------|
|`1`         | `1` | the first level|
|`2`|`1.1`|first child of ID `1`
|`3`|`1.2`|second child of ID `1`
|`4`|`1.1.1`|third level, a direct child of ID `2`
|`5`|`1.1.2`|third level, second child, also a direct child of ID `2`
|`6`|`2.1`|a child of a node not yet specified - this will work
|`7`|`2`|second first-level item

```php
$tree = new DataTree($data, new MaterializedPathTreeBuilder('position', '.'));
```

> **Note**: for now, when using IDs in hierarchy and only specify the "parents" path, you need to provide your own processing routines to the builder. See code for more details.
