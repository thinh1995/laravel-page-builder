<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class Blockable extends Model
{
    use NodeTrait;

    protected $fillable = [
        'block_id',
        'blockable_id',
        'blockable_type',
        'parent_id',
        'content',
        'order',
        'column_index',
        'locale',
    ];

    protected $casts = [
        'block_id'     => 'integer',
        'blockable_id' => 'integer',
        'parent_id'    => 'integer',
        'order'        => 'integer',
        'column_index' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('page-builder.tables.blockable'));
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}
