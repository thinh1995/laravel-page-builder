<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thinhnx\LaravelPageBuilder\Database\Factories\BlockFactory;

class Block extends Model
{
    use HasFactory;
    use Translatable;

    public $translatedAttributes = ['name', 'description', 'icon'];

    protected $with = ['translations'];

    protected $fillable = ['type', 'is_layout'];

    protected $casts = [
        'is_layout' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('page-builder.tables.block'));
    }

    protected static function newFactory(): BlockFactory
    {
        return BlockFactory::new();
    }
}
