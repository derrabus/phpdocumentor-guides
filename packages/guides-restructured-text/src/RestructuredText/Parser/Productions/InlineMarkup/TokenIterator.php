<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions\InlineMarkup;

use Iterator;
use OutOfBoundsException;

use function array_pop;

/** @implements Iterator<int, string> */
class TokenIterator implements Iterator
{
    /** @var int[] */
    private array $snapShot = [];
    private int $position = 0;

    /** @param string[] $tokens */
    public function __construct(private array $tokens)
    {
    }

    public function current(): string
    {
        if ($this->valid() === false) {
            throw new OutOfBoundsException('Attempted to token that does not exist');
        }

        return $this->tokens[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->tokens[$this->position]);
    }

    public function getNext(): string|null
    {
        return $this->tokens[$this->position + 1] ?? null;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function snapShot(): void
    {
        $this->snapShot[] = $this->position;
    }

    public function restore(): void
    {
        $this->position = array_pop($this->snapShot) ?? 0;
    }
}
