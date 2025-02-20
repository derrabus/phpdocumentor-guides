<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\Passes;

use phpDocumentor\Guides\Compiler\CompilerPass;
use phpDocumentor\Guides\Meta\DocumentEntry;
use phpDocumentor\Guides\Meta\DocumentReferenceEntry;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\Meta\SectionEntry;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\CompoundNode;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\SectionNode;
use phpDocumentor\Guides\Nodes\TocNode;

final class MetasPass implements CompilerPass
{
    public function __construct(private Metas $metas)
    {
    }

    /** {@inheritDoc} */
    public function run(array $documents): array
    {
        foreach ($documents as $document) {
            if ($document->getTitle() === null) {
                continue;
            }

            $entry = new DocumentEntry($document->getFilePath(), $document->getTitle());
            $this->traverse($document, $entry);
            $this->metas->addDocument($entry);
        }

        return $documents;
    }

    public function getPriority(): int
    {
        return 10000;
    }

    /** @param DocumentNode|SectionNode $node */
    private function traverse(CompoundNode $node, Entry $currentSection): void
    {
        foreach ($node->getChildren() as $child) {
            if ($child instanceof SectionNode) {
                $entry = new SectionEntry($child->getTitle());
                $currentSection->addChild($entry);
                $this->traverse($child, $entry);
            }

            if (!($child instanceof TocNode)) {
                continue;
            }

            //Using a DocumentReferenceMakes some sense here, however we are losing information of the TocNode,
            //So maybe we should directly inject the TOC as meta entry?
            foreach ($child->getFiles() as $file) {
                $currentSection->addChild(new DocumentReferenceEntry($file));
            }
        }
    }
}
