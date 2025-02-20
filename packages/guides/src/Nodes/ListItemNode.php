<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

/**
 * Represents a single item of a bullet or enumerated list.
 *
 * @extends CompoundNode<Node>
 */
final class ListItemNode extends CompoundNode
{
    /** @var string the list marker used for this item */
    private string $prefix;

    /** @var bool whether the list marker represents an enumerated list */
    private bool $ordered;

    /** @param Node[] $contents */
    public function __construct(string $prefix, bool $ordered, array $contents)
    {
        $this->prefix   = $prefix;
        $this->ordered  = $ordered;

        parent::__construct($contents);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function isOrdered(): bool
    {
        return $this->ordered;
    }
}
