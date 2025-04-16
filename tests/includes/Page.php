<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\includes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thinhnx\LaravelPageBuilder\Models\Traits\HasBlocks;

class Page extends Model
{
    use HasFactory;
    use HasBlocks;

    protected $table = 'pagebuilder_pages';
    protected $fillable = ['name'];

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }

    protected function setFormatItem(array &$data, Model $block): void
    {
        $data['content'] = $block->type === 'text' ? e($data['content']) : $data['content'];
    }

    public function getFormatItem(array|Model $data, Model $block): array|Model
    {
        $data['content'] = $block->type === 'text' ? htmlspecialchars_decode($data['content']) : $data['content'];

        return $data;
    }
}
