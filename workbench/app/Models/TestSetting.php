<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSetting extends Model
{
    protected $fillable = [
        'display_mode',
        'time_mode',
        'calendar_locale',
        'with_time',
    ];

    protected $casts = [
        'with_time' => 'boolean',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'display_mode' => 'amharic_combined',
                'time_mode' => 'gregorian',
                'calendar_locale' => 'am',
                'with_time' => false,
            ]
        );
    }
}
