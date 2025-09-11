<?php

namespace App\Services;

use App\Models\Organizacion;

class OrgService
{
    /** ¿childOrgId está dentro del subárbol (o mismo nodo) de rootOrgId? */
    public function inSameTree(?int $rootOrgId, ?int $childOrgId): bool
    {
        if (!$rootOrgId || !$childOrgId) return false;
        $root  = Organizacion::find($rootOrgId);
        $child = Organizacion::find($childOrgId);
        if (!$root || !$child) return false;

        $rootRuta  = $root->ruta ?? (string)$root->id . '/';
        $childRuta = $child->ruta ?? (string)$child->id . '/';

        return str_starts_with($childRuta, $rootRuta);
    }

    /** IDs de todo el subárbol (incluye la raíz) */
    public function subtreeIds(int $rootOrgId): array
    {
        $root = Organizacion::find($rootOrgId);
        if (!$root) return [];

        $prefix = ($root->ruta ?? (string)$root->id.'/');
        return Organizacion::query()
            ->where('id', $root->id)
            ->orWhere('ruta', 'like', $prefix.'%')
            ->pluck('id')
            ->all();
    }
}
