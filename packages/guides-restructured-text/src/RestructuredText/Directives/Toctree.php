<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParserContext;
use phpDocumentor\Guides\RestructuredText\Toc\ToctreeBuilder;

/**
 * Sphinx based Toctree directive.
 *
 * This directive has an issue, as the related documents are resolved on parse, but during the rendering
 * we are using the {@see Metas} to collect the titles of those documents. There is some step missing in our process
 * which could be resolved by using https://github.com/phpDocumentor/guides/pull/21?
 *
 * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/directives.html#table-of-contents
 */
class Toctree extends Directive
{
    public function __construct(private ToctreeBuilder $toctreeBuilder)
    {
    }

    public function getName(): string
    {
        return 'toctree';
    }

    /** {@inheritDoc} */
    public function process(
        DocumentParserContext $documentParserContext,
        string $variable,
        string $data,
        array $options,
    ): Node|null {
        $parserContext = $documentParserContext->getParser()->getParserContext();

        $toctreeFiles = $this->toctreeBuilder->buildToctreeFiles(
            $parserContext,
            $documentParserContext->getDocumentIterator(),
            $options,
        );

        return (new TocNode($toctreeFiles))->withOptions($this->optionsToArray($options));
    }
}
