<?php

declare(strict_types=1);

namespace App\Contracts;

interface YnabAccessTokenServiceInterface
{
    public function store(mixed $accessToken): void;

    public function get(): mixed;

    public function delete(): void;

    public function doesNotExist(): bool;
}
