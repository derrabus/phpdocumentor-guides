<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use League\Uri\Uri;
use League\Uri\UriInfo;

use function array_shift;
use function dirname;
use function ltrim;
use function strtolower;
use function trim;

class ParserContext
{
    /** @var array<string, string> */
    private array $links = [];

    /** @var string[] */
    private array $anonymous = [];

    /** @var string[] */
    private array $errors = [];

    public function __construct(
        private string $currentFileName,
        private string $currentDirectory,
        private int $initialHeaderLevel,
        private FilesystemInterface $origin,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getInitialHeaderLevel(): int
    {
        return $this->initialHeaderLevel;
    }

    public function setLink(string $name, string $url): void
    {
        $name = strtolower(trim($name));

        if ($name === '_') {
            $name = array_shift($this->anonymous);
        }

        $this->links[$name] = trim($url);
    }

    public function resetAnonymousStack(): void
    {
        $this->anonymous = [];
    }

    public function pushAnonymous(string $name): void
    {
        $this->anonymous[] = strtolower(trim($name));
    }

    /** @return array<string, string> */
    public function getLinks(): array
    {
        return $this->links;
    }

    private function relativeUrl(string $url): string
    {
        return $this->urlGenerator->relativeUrl($url);
    }

    public function absoluteRelativePath(string $url): string
    {
        $uri = Uri::createFromString($url);
        if (UriInfo::isAbsolutePath($uri)) {
            return $this->currentDirectory . '/' . ltrim($url, '/');
        }

        return $this->currentDirectory . '/' . $this->getDirName() . '/' . $this->relativeUrl($url);
    }

    public function getDirName(): string
    {
        $dirname = dirname($this->currentFileName);

        if ($dirname === '.') {
            return '';
        }

        return $dirname;
    }

    public function getCurrentFileName(): string
    {
        return $this->currentFileName;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getCurrentDirectory(): string
    {
        return $this->currentDirectory;
    }

    public function getUrl(): string
    {
        return $this->currentFileName;
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * Return the current file's absolute path on the Origin file system.
     *
     * In order to load files relative to the current file (such as embedding UML diagrams) the environment
     * must expose what the absolute path relative to the Origin is.
     *
     * @see self::setCurrentAbsolutePath() for more information
     * @see self::getOrigin() for the filesystem on which to use this path
     */
    public function getCurrentAbsolutePath(): string
    {
        return $this->urlGenerator->absoluteUrl($this->currentDirectory, $this->currentFileName);
    }

    /** @return string[] */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
