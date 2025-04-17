<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kalnoy\Nestedset\NodeTrait;
use Thinhnx\LaravelPageBuilder\Database\Factories\BlockableFactory;

class Blockable extends Model
{
    use HasFactory;
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

    /**
     * @return BlockableFactory
     */
    public static function newFactory(): BlockableFactory
    {
        return BlockableFactory::new();
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function blockable(): MorphTo
    {
        return $this->morphTo();
    }
}
