
And now an example of a delimited hierarchy string with node IDs in `parents` member.
| ID        | parents    | title|
|:----------|:----------|:-------|
|`1`         | `.` | the first level|
|`2`|`1.`|first child of `1`
|`3`|`1.`|second child of `1`
|`4`|`1.2.`|third level, a direct child of `2`
|`5`|`1.2.`|third level, second child, also a direct child of `2`
|`6`|`7.`|a child of a node not yet specified - this will work
|`7`|`.`|second first-level item

```php
$tree = new DataTree($data, new MaterializedPathTreeBuilder('parents', '.'));
```
