<?php

namespace App\Http\Controllers\Api\Candidate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CandidateRepository;
use Illuminate\Http\JsonResponse;

class CandidateController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected CandidateRepository $candidate,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create a new candidate record on the `users` table.
     *
     * @param  \App\Http\Requests\Candidate\Registration  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(\App\Http\Requests\Candidate\Registration $request): JsonResponse
    {
        // Retrieve the validated input...
        $validated = $request->validated();

        return ($this->candidate->createCandidate($validated))
            ? $this->successResponse([], 'Profile was created successfully.')
            : $this->failedResponse([], 'An error occurred while trying to create your profile.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Authenticate a candidate login request.
     *
     * @param  \App\Http\Requests\Candidate\Registration  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(\App\Http\Requests\LoginRequest $request)
    {
        return $this->successResponse($this->candidate->authentication($request), 'Login was successful.');
    }
}
