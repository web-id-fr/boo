<?php

namespace WebId\Boo\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use WebId\Boo\Models\Backup;

class BackupResource extends JsonResource
{
    /** @var Backup */
    public $resource;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'type' => Backup::TYPES[$this->resource->type],
            'status' => Backup::STATUS[$this->resource->status],
            'backup_at' => $this->resource->backup_at->format('d/m/Y H:i:s'),
        ];
    }
}
