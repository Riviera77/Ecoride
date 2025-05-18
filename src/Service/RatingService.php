<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class RatingService
{
    private Collection $collection;

    // Connexion through DNS secured to Docker MongoDB Container 
    // with services.yaml or variables of environment
    public function __construct(string $mongoDsn)
    {
        $client = new Client($mongoDsn);
        $this->collection = $client->Ecoride->ratings;
    }

    //for not write iterator_to_array($results) to each method
    //converting the result MongoDB to an array PHP (DRY)
    private function toArray($results): array
    {
        return iterator_to_array($results);
    }

    // Get/recover all ratings for a specific driver
    public function getRatingsForDriver(int $driverId): array
    {
        $results = $this->collection->find(['driverId' => (string) $driverId]);
        return $this->toArray($results);
    }

    // calculate the average rating for a specific driver
    public function getAverageRatingForDriver(int $driverId): float
    {
        $ratings = $this->getRatingsForDriver($driverId);
        
        if (count($ratings) === 0) {
            return 0;
        }

        $sum = array_sum(array_map(fn($r) => $r['rating'], $ratings));
        return round($sum / count($ratings), 1);
    }

    // Get/recover only comments for a specific driver
    public function getCommentsForDriver(int $driverId): array
    {
        $results = $this->collection->find(
            ['driverId' => (string) $driverId],
            ['projection' => ['comment' => 1, '_id' => 0]]
        );

        return $this->toArray($results);
    }

    // Add a rating and a comment for a specific driver
    public function addRating(int $driverId, int $authorId, int $carpoolingId, float $rating, string $comment): void
    {
        $this->collection->insertOne([
            'driverId' => (string) $driverId,
            'authorId' => (string) $authorId,
            'carpoolingId' => (string) $carpoolingId,
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'pending',
            'createdAt' => "2025-12-02T10:00:00Z"
        ]);
    }

    // Get all reviews with status 'pending'
    public function getPendingReviews(): array
    {
        $results = $this->collection->find([
            'status' => 'pending'
        ]);

        return $this->toArray($results);
    }

    // Approve a review
    public function approveReview(string $id): void
    {
        $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => ['status' => 'approved']]
        );
    }

    // reject a review
    public function rejectReview(string $id): void
    {
        $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => ['status' => 'rejected']]
        );
    }
}