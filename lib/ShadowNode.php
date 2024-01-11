<?php

declare(strict_types=1);

namespace Dakujem\Oliva;

/**
 * ShadowNode
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class ShadowNode implements MovableNodeContract
{
    public function __construct(
        TreeNodeContract $realNode,
        $path,
        $key,
    ) {
    }
}
