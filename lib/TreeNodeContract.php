<?php

declare(strict_types=1);

namespace Dakujem\Oliva;

/**
 * Base contract supporting tree traversals.
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface TreeNodeContract
{
    public function parent(): ?TreeNodeContract;

    public function children(): iterable;

    /**
     * Returns `true` if the node has no children.
     */
    public function isLeaf(): bool;

    /**
     * Get a specific child.
     *
     * @param string|int $index
     * @return mixed
     */
    public function child(/*string|int*/ $index): ?TreeNodeContract;

    /**
     * @return string|int|null
     */
    public function childIndex(TreeNodeContract $node);//:mixed;
}
