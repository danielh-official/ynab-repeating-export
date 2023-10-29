<?php

namespace App\Transformers;

use App\Contracts\TransformerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;

class YnabPayeeTransformer extends BaseTransformer
{
    protected string $sessionKey = 'ynab.payees';
}
