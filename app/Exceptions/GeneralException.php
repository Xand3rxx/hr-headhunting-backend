<?php

namespace App\Exceptions;

use Exception;

class GeneralException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        // ...
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render()
    {
        return \Illuminate\Support\Facades\Redirect::back()->with('error', $this->message)->withInput();
    }
}
