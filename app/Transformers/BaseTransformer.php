<?php

namespace App\Transformers;

use App\Contracts\TransformerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;

class BaseTransformer implements TransformerInterface
{
    protected string $sessionKey;

    /**
     * @param array $data
     * @return void
     */
    public function store(array $data): void
    {
        $session = Session::get($this->sessionKey, []);

        if (is_array($session)) {
            $item = $data;

            $id = data_get($item, 'id');

            if ($id) {
                $hasSameIdAsExisting = collect($session)->contains(function ($value) use ($id) {
                    return data_get($value, 'id') === $id;
                });

                if ($hasSameIdAsExisting) {
                    $updatedItem = collect($session)->first(function ($value) use ($id) {
                        return data_get($value, 'id') === $id;
                    });

                    $updatedItem = array_merge($updatedItem, $item);

                    $indexOfUpdatedItem = collect($session)->search(function ($value) use ($id) {
                        return data_get($value, 'id') === $id;
                    });

                    Session::forget($this->sessionKey . '.' . $indexOfUpdatedItem);

                    Session::push($this->sessionKey, $updatedItem);

                    $data = Session::get($this->sessionKey);

                    $data = array_values($data);

                    Session::put($this->sessionKey, $data);
                } else {
                    Session::push($this->sessionKey, $item);
                }
            }
        } else {
            Session::put($this->sessionKey, [$data]);
        }
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        $data = Session::get($this->sessionKey, new Collection());

        if (!$data instanceof Collection) {
            $data = new Collection($data);
        }

        return $data;
    }
}
