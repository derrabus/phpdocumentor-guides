<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;

final class RenderCommand
{
    /** @param DocumentNode[] $documents */
    public function __construct(
        private string $outputFormat,
        private array $documents,
        private Metas $metas,
        private FilesystemInterface $origin,
        private FilesystemInterface $destination,
        private string $destinationPath = '/',
    ) {
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    /** @return DocumentNode[] */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function getMetas(): Metas
    {
        return $this->metas;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getDestination(): FilesystemInterface
    {
        return $this->destination;
    }

    public function getDestinationPath(): string
    {
        return $this->destinationPath;
    }
}
