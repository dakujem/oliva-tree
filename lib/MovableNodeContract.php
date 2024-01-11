<?php

declare(strict_types=1);

namespace Dakujem\Oliva;


/**
 * Low-level contract for manipulation with nodes.
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface MovableNodeContract extends TreeNodeContract
{
    public function addChild(TreeNodeContract $child, string|int|null $index = null): self;

    public function removeChild(TreeNodeContract|string|int $child): self;

    public function setParent(?TreeNodeContract $parent): self;
}