<?php

namespace App\Validators;

use App\Exceptions\BadRequest;
use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Review as ReviewModel;
use App\Repos\Review as ReviewRepo;
use App\Repos\ReviewLike as ReviewLikeRepo;

class Review extends Validator
{

    public function checkReview($id)
    {
        $reviewRepo = new ReviewRepo();

        $review = $reviewRepo->findById($id);

        if (!$review) {
            throw new BadRequestException('review.not_found');
        }

        return $review;
    }

    public function checkCourse($id)
    {
        $validator = new Course();

        return $validator->checkCourse($id);
    }

    public function checkContent($content)
    {
        $value = $this->filter->sanitize($content, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 10) {
            throw new BadRequestException('review.content_too_short');
        }

        if ($length > 255) {
            throw new BadRequestException('review.content_too_long');
        }

        return $value;
    }

    public function checkRating($rating)
    {
        if (!in_array($rating, [1, 2, 3, 4, 5])) {
            throw new BadRequestException('review.invalid_rating');
        }

        return $rating;
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('review.invalid_publish_status');
        }

        return $status;
    }

    public function checkIfAllowEdit(ReviewModel $review)
    {
        $case = time() - $review->create_time > 7 * 86400;

        if ($case) {
            throw new BadRequestException('review.edit_not_allowed');
        }
    }

    public function checkIfLiked($reviewId, $userId)
    {
        $repo = new ReviewLikeRepo();

        $like = $repo->findReviewLike($reviewId, $userId);

        if ($like) {
            if ($like->deleted == 0 && time() - $like->create_time > 5 * 60) {
                throw new BadRequestException('review.has_liked');
            }
        }

        return $like;
    }

}
