<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

class IncidentService
{
    private Collection $collection;

    /**
     * On injecte directement le client MongoDB existant (déjà configuré dans services.yaml)
     */
    public function __construct(Client $mongoClient)
    {

        // Sélectionne la base 'Ecoride' et la collection 'incidents'
        $this->collection = $mongoClient->Ecoride->incidents;
    }

    /**
     * @return array<mixed>
     */
    public function getAllIncidents(): array
    {
     // récupère tous les signqlements de trajets
        // je convertie l'itérateur MongoDB en tableau PHP
        /** @var \Traversable $cursor */
        return iterator_to_array($this->collection->find());
    }

    /**
     * Ajoute un signalement de trajet mal passé
     *
     * @param int $carpoolingId ID du trajet concerné
     * @param int $reporterId ID du passager qui signale
     * @param string $message Motif du signalement
     */
    public function addIncident(int $carpoolingId, int $reporterId, string $message): void
    {
        $this->collection->insertOne([
            'carpoolingId' => (string) $carpoolingId,
            'reporterId' => (string) $reporterId,
            'message' => $message,
            /** @var \MongoDB\BSON\UTCDateTime $createdAt */
            'createdAt' => new UTCDateTime()
        ]);
    }
}