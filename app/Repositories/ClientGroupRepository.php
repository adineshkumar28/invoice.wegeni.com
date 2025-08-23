<?php

namespace App\Repositories;

use App\Models\ClientGroup;

/**
 * Class ClientGroupRepository
 */
class ClientGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
    ];

    /**
     * Return searchable fields
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return ClientGroup::class;
    }
}
