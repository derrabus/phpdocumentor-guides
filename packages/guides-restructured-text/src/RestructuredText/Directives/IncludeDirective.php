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

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\LiteralBlockNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Nodes\CollectionNode;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParserContext;
use RuntimeException;

use function array_key_exists;
use function explode;
use function sprintf;
use function str_replace;

final class IncludeDirective extends Directive
{
    public function getName(): string
    {
        return 'include';
    }

    /** {@inheritDoc} */
    public function processNode(
        DocumentParserContext $documentParserContext,
        string $variable,
        string $data,
        array $options,
    ): Node {
        $parser = $documentParserContext->getParser();
        $subParser = $parser->getSubParser();
        $parserContext = $parser->getParserContext();
        $path = $parserContext->absoluteRelativePath($data);

        $origin = $parserContext->getOrigin();
        if (!$origin->has($path)) {
            throw new RuntimeException(
                sprintf('Include "%s" (%s) does not exist or is not readable.', $data, $path),
            );
        }

        $contents = $origin->read($path);

        if ($contents === false) {
            throw new RuntimeException(sprintf('Could not load file from path %s', $path));
        }

        if (array_key_exists('literal', $options)) {
            $contents = str_replace("\r\n", "\n", $contents);

            return new LiteralBlockNode($contents);
        }

        if (array_key_exists('code', $options)) {
            $contents = str_replace("\r\n", "\n", $contents);
            $codeNode = new CodeNode(
                explode('\n', $contents),
            );
            $codeNode->setLanguage($options['code']);

            return $codeNode;
        }

        return new CollectionNode($subParser->parse($parser->getParserContext(), $contents)->getChildren());
    }
}
