<?php

namespace App\Observers;

use App\Models\Kategori;
use App\Services\Search\CatalogIndex;
use Illuminate\Database\Eloquent\Model;

final class SearchCatalogObserver
{
    public function saved(Model $model): void
    {
        app(CatalogIndex::class)->changed($model instanceof Kategori ? 'category' : 'product', (string) $model->getKey());
    }

    public function deleted(Model $model): void
    {
        $this->saved($model);
    }
}
