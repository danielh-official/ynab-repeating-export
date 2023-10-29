<?php

namespace App\Services;

use Illuminate\Http\Request;

class YnabLastKnowledgeOfServerService
{
    public function store(int $knowledgeOfServer, Request $request): void
    {
        $request->session()->put('ynab_last_knowledge_of_server', $knowledgeOfServer);
    }

    public function get(): int|null
    {
        return session('ynab_last_knowledge_of_server');
    }
}
