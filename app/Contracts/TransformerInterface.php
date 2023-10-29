<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TransformerInterface
{
    public function store(array $data): void;

    public function get(): Collection;
}
