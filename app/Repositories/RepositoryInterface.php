<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function all();

    public function create(array $data, $files = []);

    public function update(array $data, Model $record, $files = []): bool;

    public function delete(Model $record);

    public function get($id);
}
