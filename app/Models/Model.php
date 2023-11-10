<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * @method static create(array $array)
 * @method create(array $array)
 * @method where(string $column, string $symbol, mixed $value)
 */
abstract class Model extends BaseModel implements ModelInterface
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];
}
