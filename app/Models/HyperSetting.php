<?php

namespace Hyperodactyl\Models;

/**
 * Hyperodactyl\Models\HyperSetting.
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 */
class HyperSetting extends Model
{
    protected $table = 'hyper_settings';

    protected $fillable = ['key', 'value'];

    public static array $validationRules = [
        'key' => 'required|string|between:1,191',
        'value' => 'nullable|string',
    ];
}
