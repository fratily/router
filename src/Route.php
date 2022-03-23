<?php

namespace Fratily\Router;

use InvalidArgumentException;

class Route
{
    private ?string $name = null;

    private mixed $payload = null;

    /**
     * @param string $path The matching path string.
     * @param RouteOption $option The matching rule option.
     */
    public function __construct(
        private string $path,
        private RouteOption $option
    ) {
        $this->path = $this->path($path)->path; // call path method for validate.
        $this->option = $option;
    }

    /**
     * Returns the path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the matching path.
     */
    public function path(string $path): self
    {
        if ($path === '') {
            throw new InvalidArgumentException('The path must not be an empty string.');
        }

        if (trim($path) !== $path) {
            throw new InvalidArgumentException('The path must not start or end with a space.');
        }

        if (!str_starts_with($path, '/')) {
            throw new InvalidArgumentException('The path must start with a slash.');
        }

        if (str_contains($path, '//')) {
            throw new InvalidArgumentException('The path must not contain consecutive slashes.');
        }

        /** @var string[] Regular expressions are fine, so they can never be false */
        $mb_splitted_path = preg_split('//u' , $path, -1, PREG_SPLIT_NO_EMPTY);
        $mb_length = count($mb_splitted_path);
        if (strlen($path) !== $mb_length) {
            throw new InvalidArgumentException('The path must not contain multibyte characters.');
        }

        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * Returns the matching rule option.
     */
    public function getOption(): RouteOption
    {
        return $this->option;
    }

    /**
     * Set the matching rule option.
     */
    public function option(RouteOption $option): self
    {
        $clone = clone $this;
        $clone->option = $option;
        return $clone;
    }

    /**
     * Returns the name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name.
     */
    public function name(?string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /**
     * Returns the payload.
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }

    /**
     * Set the payload.
     */
    public function payload(mixed $payload): self
    {
        $clone = clone $this;
        $clone->payload = $payload;
        return $clone;
    }
}
