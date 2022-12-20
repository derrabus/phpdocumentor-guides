<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\NodeRenderers\LaTeX;

use InvalidArgumentException;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\UrlGenerator;

use function ltrim;

class TocNodeRenderer implements NodeRenderer
{
    private Renderer $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render(Node $node, RenderContext $environment): string
    {
        if ($node instanceof TocNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        return $this->renderer->render(
            'toc.tex.twig',
            [
                'tocNode' => $node,
            ]
        );
    }

    public function supports(Node $node): bool
    {
        return $node instanceof TocNode;
    }
}
