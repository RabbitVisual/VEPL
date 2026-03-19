<?php

declare(strict_types=1);

namespace Modules\Community\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumCategory extends Model
{
    protected $table = 'forum_categories';

    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class, 'category_id');
    }
}
