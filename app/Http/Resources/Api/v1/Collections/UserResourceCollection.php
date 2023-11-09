<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\v1\Collections;

use App\Http\Resources\Api\v1\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = UserResource::class;
}
