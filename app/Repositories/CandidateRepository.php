<?php

namespace App\Repositories;

use App\Interfaces\CandidateRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CandidateRepository implements CandidateRepositoryInterface
{
    /**
     *  Repository function to authenticate a candidate.
     */
    public function authentication($payload)
    {
        // Instance of the Authentication service
        $auth = new \App\Services\Authentication();
        return $auth->handle($payload);
    }

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
            throwException('Candidate not found.', Response::HTTP_NOT_FOUND);
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
