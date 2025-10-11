<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $authenticatedUserRoleID = Auth::user()->role_id;

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(Auth::user()->getIndexRoute($authenticatedUserRoleID))
                    : Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
