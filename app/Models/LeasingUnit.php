<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeasingUnit extends Model
{
    public const FLOORS = [
        'ground' => ['al' => 'Kati 0', 'en' => 'Ground Floor', 'number' => '0'],
        'minus-one' => ['al' => 'Kati -1', 'en' => '-1 Floor', 'number' => '-1'],
        'third' => ['al' => 'Kati 3', 'en' => '3rd Floor', 'number' => '3'],
        'roof' => ['al' => 'Tarraca L+4', 'en' => 'Roof L+4', 'number' => '4'],
        'exterior' => ['al' => 'Hapësirat e jashtme', 'en' => 'Exterior Boxes', 'number' => ''],
    ];

    public $timestamps = false;

    public function label(string $locale = 'en'): string
    {
        return self::FLOORS[$this->floor][$locale].' — '.$this->code;
    }
}
