<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown\Parsers;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock as CommonMarkListBlock;
use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use phpDocumentor\Guides\MarkupLanguageParser;
use phpDocumentor\Guides\Nodes\CompoundNode;
use phpDocumentor\Guides\Nodes\ListNode;

/** @extends AbstractBlock<ListNode> */
final class ListBlock extends AbstractBlock
{
    /** @return ListNode */
    public function parse(MarkupLanguageParser $parser, NodeWalker $walker): CompoundNode
    {
        $context = new ListNode([], false);

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering()) {
                continue;
            }

            if ($node instanceof CommonMarkListBlock) {
                return $context;
            }

            echo 'LIST CONTEXT: I am '
                . 'leaving'
                . ' a '
                . $node::class
                . ' node'
                . "\n";
        }

        return $context;
    }

    public function supports(NodeWalkerEvent $event): bool
    {
        return $event->isEntering() && $event->getNode() instanceof CommonMarkListBlock;
    }
}
