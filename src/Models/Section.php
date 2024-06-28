<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use MattDaneshvar\Survey\Contracts\Question;
use MattDaneshvar\Survey\Contracts\Section as SectionContract;
use App\Traits\AutoGenerateUuid;

class Section extends Model implements SectionContract
{
    use HasTranslations, AutoGenerateUuid;

    /**
     * @var bool $incrementing
     */
    public $incrementing = false;

    /**
     * @var string $keyType
     */
    protected $keyType = 'string';

    public $translatable = ['name'];

    /**
     * Section constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (! isset($this->table)) {
            $this->setTable(config('survey.database.tables.sections'));
        }

        parent::__construct($attributes);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The questions of the section.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(get_class(app()->make(Question::class)));
    }
}
