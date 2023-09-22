<?php

namespace App\Repositories;

use App\Interfaces\CandidateRepositoryInterface;
use App\Models\User;

class CandidateRepository implements CandidateRepositoryInterface
{
    /**
     *  Repository function to get all candidates.
     */
    public function getCandidates()
    {
        return User::all();
    }

    /**
     *  Repository function to get a candidate by UUID.
     */
    public function getCandidate($uuid)
    {
        $user = User::whereUuid($uuid)->first();

        if (empty($user)) {
            throwException('Candidate not found.');
        }

        return $user;
    }

    /**
     *  Repository function to create a new candidate.
     */
    public function createCandidate(array $user)
    {
        return User::create($user);
    }

    /**
     *  Repository function to update the record of a candidate.
     */
    public function updateCandidate($uuid, array $payload)
    {
        $user = self::getCandidate($uuid);
        return $user->update($payload);
    }

    /**
     *  Repository function to delete a candidate by UUID.
     */
    public function deleteCandidate($uuid)
    {
        $user = self::getCandidate($uuid);
        return $user->delete();
    }
}
