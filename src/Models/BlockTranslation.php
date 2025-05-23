<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Thinhnx\LaravelPageBuilder\Database\Factories\BlockTranslationFactory;

class BlockTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'locale',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('page-builder.tables.block_translation'));
    }

    /**
     * @return BlockTranslationFactory
     */
    public static function newFactory(): BlockTranslationFactory
    {
        return BlockTranslationFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(config('page-builder.models.block'), 'block_id');
    }
}
