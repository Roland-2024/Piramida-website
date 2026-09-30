<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpaceType;
use App\Models\LeasingUnit;

class LeasingController extends SpaceController
{
    protected string $routePrefix = 'admin.leasing';

    protected string $singular = 'Leasing space';

    protected string $plural = 'Leasing spaces';

    protected SpaceType $spaceType = SpaceType::Leasing;

    protected function globalFields(): array
    {
        return [
            [
                'name' => 'leasing_unit_id', 'label' => 'Floor / map unit', 'type' => 'select',
                'options' => ['' => 'Not assigned (unavailable)'] + LeasingUnit::query()
                    ->orderBy('display_order')->get()->mapWithKeys(fn (LeasingUnit $unit) => [$unit->id => $unit->label()])->all(),
            ],
            ['name' => 'is_available', 'label' => 'Available for applications', 'type' => 'checkbox'],
            ...parent::globalFields(),
        ];
    }
}
