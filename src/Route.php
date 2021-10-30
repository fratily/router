<?php

namespace Fratily\Router;

use InvalidArgumentException;

class Route
{
    private string $path;

    /**
     * @var string[]
     */
    private array $methods;

    private ?bool $isStrictCheckTrailing = null;

    private ?string $name;

    private mixed $payload = null;

    /**
     * Constructor.
     *
     * @param string $path The matching path string.
     * @param string[] $methods The allow http methods.
     * @param string|null $name The route name.
     */
    public function __construct(string $path, array $methods, ?string $name = null)
    {
        $this->path($path)->methods($methods);
        $this->name = $name;
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
            throw new InvalidArgumentException('The path must not start or end with a space');
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

        $this->path = $path;

        return $this;
    }

    /**
     * Returns the methods.
     *
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Set the matching HTTP methods.
     *
     * @param string[] $methods
     */
    public function methods(array $methods): self
    {
        if (count($methods) === 0) {
            throw new InvalidArgumentException('The HTTP methods must be set.');
        }

        if ($methods !== array_values($methods)) {
            throw new InvalidArgumentException('The HTTP methods must be of list type');
        }

        foreach ($methods as $method) {
            if (!is_string($method) || $method === '') { // @phpstan-ignore-line
                throw new InvalidArgumentException('The HTTP method must be a non empty string');
            }

            if (trim($method) !== $method) {
                throw new InvalidArgumentException('The HTTP method must not start or end with a space');
            }
        }

        if ($methods !== array_unique($methods)) {
            throw new InvalidArgumentException('The HTTP method must be a unique value in list');
        }

        $this->methods = $methods;

        return $this;
    }

    /**
     * Returns the name.
     */
    public function getName(): ?string
    {
        return $this->name;
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
        $this->payload = $payload;

        return $this;
    }

    /**
     * Returns the strict check trailing setting.
     */
    public function isStrictCheckTrailing(): ?bool
    {
        return $this->isStrictCheckTrailing;
    }

    /**
     * Set to true to ensure the presence or absence of trailing slash.
     */
    public function strictCheckTrailing(?bool $isStrictCheckTrailing = true): self
    {
        $this->isStrictCheckTrailing = $isStrictCheckTrailing;

        return $this;
    }
}
