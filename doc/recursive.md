[Oliva Tree](docs.md) > Recursive tree

## Recursive tree

A trivial data tree where each data-node has a pointer to its parent. This data model is called the Adjacency List Model and when stored in a database, it is often referred to as the self-joined table design, self-referencing or self-referenced tables and so on.

```
[1] (root) parent: NULL
 |
 +-- [2] parent: 1
 |    |
 |    +-- [6] parent: 2
 |    |    |
 |    |    +-- [4] parent: 6
 |    |
 |    +-- [5] parent: 2
 |
 +-- [3] parent: 1
```

Stored in a database table:

| ID        | parent    | title|
|:----------|:----------|:-------|
|1          | NULL| the root|
|2|1|first child of root
|3|1|second child of root
|4|6|fourth level - parent specified later - this will work
|5|2|third level
|6|2|third level, second child

Get this structure from a database as an array of rows.
Tell the tree that "parent" is the member where the parent's "id" is found.
Create the tree.
```php
$root = (new RecursiveTreeBuilder('parent', 'id'))->build(MyDatabase::fetchAll());
```
> Note: if you have more than one root, the builder's behaviour is undefined, the trees will overwrite one another.



----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`RecursiveTreeBuilder` | `Oliva\Utils\Tree\Builder\RecursiveTreeBuilder` | [RecursiveTreeBuilder.php](../src/Builder/RecursiveTreeBuilder.php) ||
