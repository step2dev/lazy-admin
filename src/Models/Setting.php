<?php

namespace Step2dev\LazyAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getDescriptionAttribute(): string
    {
        return __('settings.'.$this->settingKey.'.desc');
    }

    public function getSettingKeyAttribute(): string
    {
        return $this->group.'.'.$this->key;
    }
}
