<?php

namespace Step2dev\LazyAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $group
 * @property string $key
 * @property string $value
 * @property string $type
 * @property bool $is_protected
 * @property bool $is_encrypted
 * @property bool $deletable
 * @property-read string $settingKey
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'type',
        'value',
        'is_protected',
        'is_encrypted',
        'deletable',
    ];

    protected $casts = [
        'group' => 'string',
        'key' => 'string',
        'type' => 'string',
        'is_protected' => 'boolean',
        'is_encrypted' => 'boolean',
        'deletable' => 'boolean',
    ];

    public function getDescriptionAttribute(): string
    {
        return __('settings.'.$this->settingKey.'.desc');
    }

    public function getSettingKeyAttribute(): string
    {
        return $this->group.'.'.$this->key;
    }
}
