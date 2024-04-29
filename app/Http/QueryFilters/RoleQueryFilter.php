<?php

namespace App\Http\QueryFilters;

use Illuminate\Support\Facades\Auth;

class RoleQueryFilter extends QueryFilter
{
    protected $searchable = [
        'name',
    ];

    protected $sortable = [
        'name', 'updated_at'
    ];

    protected $defaultSortBy = 'updated_at';


    public function indexQuery()
    {
        /** @var \App\Models\User $user*/
        $user = Auth::user();

        if (!$user->isSuperAdmin()) {
            $this->queryBuilder->whereNot('name', 'super_admin');
        }
    }
}
