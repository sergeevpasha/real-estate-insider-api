<?php

declare(strict_types=1);

namespace App\Dto;

use App\Helpers\ArrayHelper;

abstract class AbstractDto
{

    /**
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * @return array
     */
    public function toNotNullableArray(): array
    {
        return ArrayHelper::removeNullValuesRecursively($this->toArray());
    }
}