<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use InvalidArgumentException;
use phpDocumentor\Guides\MarkupLanguageParser as ParserInterface;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParserContext;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\Rule;
use RuntimeException;
use Webmozart\Assert\Assert;

use function strtolower;

class MarkupLanguageParser implements ParserInterface
{
    private ParserContext|null $parserContext = null;

    private string|null $filename = null;

    private DocumentParserContext|null $documentParser = null;

    /** @param Rule<DocumentNode> $startingRule */
    public function __construct(private Rule $startingRule)
    {
    }

    public function supports(string $inputFormat): bool
    {
        return strtolower($inputFormat) === 'rst';
    }

    /** @deprecated one should use injected rules in a rule. Not subparsers */
    public function getSubParser(): MarkupLanguageParser
    {
        return new MarkupLanguageParser(
            $this->startingRule,
        );
    }

    public function getParserContext(): ParserContext
    {
        if ($this->parserContext === null) {
            throw new RuntimeException(
                'A parser\'s Environment should not be consulted before parsing has started',
            );
        }

        return $this->parserContext;
    }

    public function getDocument(): DocumentNode
    {
        if ($this->documentParser === null) {
            throw new RuntimeException('Nothing has been parsed yet.');
        }

        return $this->documentParser->getDocument();
    }

    public function getFilename(): string
    {
        return $this->filename ?: '(unknown)';
    }

    public function parse(ParserContext $parserContext, string $contents): DocumentNode
    {
        $this->parserContext = $parserContext;

        $this->documentParser = new DocumentParserContext($contents, $parserContext, $this);

        if ($this->startingRule->applies($this->documentParser)) {
            $document = $this->startingRule->apply($this->documentParser);
            Assert::isInstanceOf($document, DocumentNode::class);

            return $document;
        }

        throw new InvalidArgumentException('Content is not a valid document content');
    }

    /** @deprecated this should be replaced by proper usage of productions in other productions, by now this is a hack. */
    public function parseFragment(DocumentParserContext $documentParserContext, string $contents): DocumentNode
    {
        $documentParserContext = $documentParserContext->withContents($contents);
        if ($this->startingRule->applies($documentParserContext)) {
            $document = $this->startingRule->apply($documentParserContext);
            Assert::isInstanceOf($document, DocumentNode::class);

            return $document;
        }

        throw new InvalidArgumentException('Content is not a valid document content');
    }
}
