<?php

namespace App\Interfaces;

interface CandidateRepositoryInterface
{
    public function getCandidates();
    public function getCandidate($uuid);
    public function createCandidate(array $payload);
    public function updateCandidate($uuid, array $payload);
    public function deleteCandidate($uuid);
}
