<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpaceType;
use App\Models\LeasingUnit;
use App\Models\Media;

class LeasingController extends SpaceController
{
    protected string $routePrefix = 'admin.leasing';

    protected string $singular = 'Leasing space';

    protected string $plural = 'Leasing spaces';

    protected SpaceType $spaceType = SpaceType::Leasing;

    protected function globalFields(): array
    {
        return [
            ['name' => 'floor_plan_media_id', 'label' => 'Floor plan (PDF)', 'type' => 'select',
                'options' => ['' => 'No floor plan'] + Media::query()->where('disk', 'public')
                    ->where('mime_type', 'application/pdf')->orderBy('original_name')->orderBy('id')
                    ->get()->mapWithKeys(fn (Media $media) => [$media->id => $media->original_name.' (#'.$media->id.')'])->all()],
            [
                'name' => 'leasing_unit_id', 'label' => 'Floor / map unit', 'type' => 'select',
                'options' => ['' => 'Not assigned (unavailable)'] + LeasingUnit::query()
                    ->orderBy('display_order')->get()->mapWithKeys(fn (LeasingUnit $unit) => [$unit->id => $unit->label()])->all(),
            ],
            ['name' => 'is_available', 'label' => 'Available for applications', 'type' => 'checkbox'],
            ...array_map(fn (array $field) => match ($field['name']) {
                'price_from' => [...$field, 'label' => 'Minimum offer per m² (ALL)'],
                'currency' => [...$field, 'default' => 'ALL'],
                default => $field,
            }, parent::globalFields()),
        ];
    }
}
