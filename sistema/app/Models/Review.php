<?php

namespace App\Models;

use App\Enums\BoolStatus;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use CrudTrait;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'search_id', 'user_id', 'check_status', 'text', 'status', 'created_at', 'updated_at'
    ];

    public function reviewSources()
    {
        return $this->hasMany(ReviewSource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function search(): BelongsTo
    {
        return $this->belongsTo(Search::class);
    }

    public function searchWithObject(): BelongsTo
    {
        return $this->belongsTo(SearchWithObject::class, 'search_id', 'id');
    }

    public function getText()
    {
        return $this->text;
    }

    public function showManageBtn()
    {
        return manageBtn('review.show', $this->id);
    }

    public function addReviewSource()
    {
        $link = route("review-source.create") . '?review_id=' . $this->id . "&user_id=" . $this->user_id;
        return customHtmlLink($link, 'la la-link text-success', __("Add source"));
    }

    public function showReviewSource()
    {
        $link = route("review-source.index") . '?review_id=' . $this->id . "&user_id=" . $this->user_id;
        return customHtmlLink($link, 'la la-link text-success', __("Show sources"));
    }

    public function getFormatedSources()
    {
        $responseData = [];
        $activeSources = $this->reviewSources()->whereStatus(BoolStatus::Active)->get();
        foreach ($activeSources as $source) {
            $responseData[] = [
                'notes' => $source->notes,
                'created_at' => $source['created_at'],
                'updated_at' => $source['updated_at']
            ];
        }
        return $responseData;
    }

}
