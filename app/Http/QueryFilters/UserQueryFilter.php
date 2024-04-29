<?php

namespace App\Http\QueryFilters;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserQueryFilter extends QueryFilter
{
    protected $searchable = [
        'first_name',
        'last_name',
        'email',
        'phone_number'
    ];

    protected $sortable = [
        'name', 'created_at'
    ];

    protected $defaultSortBy = 'updated_at';

    public function indexQuery()
    {
        /** @var \App\Models\User $user*/
        $user = Auth::user();

        if (!$user->isSuperAdmin()) {
            $this->queryBuilder->withoutSuperAdmins();
        }
    }

    public function onlyParent()
    {
        return $this->queryBuilder
            ->whereNull('parent_id')
            ->with('children');
    }

    public function roles($value)
    {
        if (!$value) {
            return;
        }

        $values = explode(',', $value);

        return $this->queryBuilder
            ->whereHas('roles', function ($query) use ($values) {
                $query->whereIn('name', $values);
            });
    }
}
