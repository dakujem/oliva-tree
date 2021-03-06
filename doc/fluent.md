[Oliva Tree](docs.md) > Fluent tree building

## Fluent tree building

Another cool feature. Both `Node` and `SimpleNode` descend from `NodeBase` that provides methods for fluent tree building (among other stuff).
```
$root = new SimpleNode('0');
$root->addNode('1')
       ->addNode('1.1')
           ->addLeaf('1.1.1')
           ->addLeaf('1.1.2')
           ->getParent()
       ->addLeaf('1.2')
       ->addNode('1.3')
           ->addLeaf('1.3.1')
           ->addLeaf('1.3.2')
           ->getNthAncestor(2) // === ->getParent()->getParent()
  ->addLeaf('2');
  ```
  The same works with any `NodeBase` descendant or any class using the `FluentNodeTrait` trait.

----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`Node` | `Oliva\Utils\Tree\Node\Node` | [Node.php](../src/Node/Node.php) |[Nodes](nodes.md)|
|`SimpleNode` | `Oliva\Utils\Tree\Node\SimpleNode` | [SimpleNode.php](../src/Node/SimpleNode.php) |[Nodes](nodes.md)|
|`NodeBase` | `Oliva\Utils\Tree\Node\NodeBase` | [NodeBase.php](../src/Node/NodeBase.php) |[Nodes](nodes.md)|
|`FluentNodeTrait` | `Oliva\Utils\Tree\Node\FluentNodeTrait` | [FluentNodeTrait.php](../src/Node/FluentNodeTrait.php) ||

