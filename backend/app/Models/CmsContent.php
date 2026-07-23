<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    protected $table = 'cms_contents';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getAllAsKeyValue(): array
    {
        return static::query()->pluck('value', 'key')->toArray();
    }
}
