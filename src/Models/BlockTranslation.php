<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('page-builder.tables.block_translation'));
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}
