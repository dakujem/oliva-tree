[Oliva Tree](docs.md) > Materialized path tree

## Materialized path tree

`MaterializedPathTreeBuilder` builds trees from data that follow the Materialized Paths data model.
The position of a node in a tree is represented by a hierarchy string.

There are more variants of the Path trees, some use fixed length strings for each depth level, some use delimited strings.
The hierarchy may be represented by parent nodes' IDs (parent references) or by sibling numbers (sequence numerators).
In many cases, the references and sibling numbers are encoded to reduce the path string length.

And the good news is `MaterializedPathTreeBuilder` can handle them all.


> **Note**: you do not have to specify the root in the data, it will be added automatically.
> This is useful for components like menus.


### Path trees with sibling numbers

Path trees with sibling numbers are useful for storing menu hierarchy or similar structures where position of a node within a sub-tree matters.
A *sibling number* (I saw it somewhere under the name *sequence numerator*) is simply a number of an ordered sequence representing the position,
for example when the node is second within its siblings, it's number may be 2, depending on implementation.
In large data sets with long IDs this approach may also reduce data size (the path string is considerably shorter).

#### Fixed-length

In this example, each depth level is represented by a fixed-length string representing
the position of the node in the current sub-tree (3 characters per depth level)
and sibling numbers are used (starting with 1).
This allows ordering of leafs and is useful for creating for example menu components.

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
$root = (new MaterializedPathTreeBuilder('hierarchy', 3))->build(MyDatabase::fetchAll());
```
Unless specified with NULL position (or position that is shorter than number of characters needed for each level),
an empty root node will be created to wrap the data tree. You can provide a data item with NULL position to specify your own root.

> **Note**: if you have more than one root, the later will override the previous one.


#### Delimited strings

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
$root = (new MaterializedPathTreeBuilder('position', '.'))->build(MyDatabase::fetchAll());
```

> **Note**: encoding of the references or sibling numbers won't matter for the builder when using sibling numbers.


### Path trees with parent references

Having parent references in the position strings means that they contain unique references that directly identify parent nodes,
for example IDs.


And now an example of a delimited hierarchy string with node IDs in `parents` member.

| ID        | parents    | title|
|:----------|:----------|:-------|
|`1`         | `.` | the first level|
|`2`|`1.`|first child of `1`
|`3`|`1.`|second child of `1`
|`4`|`1.2.`|third level, a direct child of `2`
|`5`|`1.2.`|third level, second child, also a direct child of `2`
|`6`|`7.`|a child of a node with ID 7 not yet specified - this will work
|`7`|`.`|second first-level item

```php
$root = (new MaterializedPathTreeBuilder('parents', '.'))->build(MyDatabase::fetchAll());
```


> **Note**: for now, when using IDs in hierarchy and only specifying the parents in the "path", you need to provide your own processing routines to the builder.
> See code for more details.


### Gap bridging
`MaterializedPathTreeBuilder` has one useful option - it can fill the gaps when there are references to non-existent nodes in the hierarchy strings.
This means that:
* the order of the nodes in the data does not matter
* you will always get a consistent tree structure

Like in this case:

| ID        | position    | title|
|:----------|:----------|:-------|
|`2`|`1.1`|first child of ID `1`

The resulting tree will have an empty root, its empty child, and a leaf node with ID 2.
```
(empty root)
 |
 +-- (empty)
      |
      +-- [node ID 2]
```

Or in this case:

| ID        | position    | title|
|:----------|:----------|:-------|
|`2`|`1`|a node
|`1`|`NULL`|the root is specified after a node

```
[root node ID 1]
 |
 +-- [node ID 2]
```

----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`MaterializedPathTreeBuilder` | `Oliva\Utils\Tree\Builder\MaterializedPathTreeBuilder` | [Mate...Builder.php](../src/Builder/MaterializedPathTreeBuilder.php) ||
