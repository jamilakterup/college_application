<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'Admission/HSC/checkMerit',
        'Admission/Honours/checkMerit',
        'Hsc/promotion/check',
        'Degree/formfillup/check',
        'Masters/formfillup/check',
        'Masters1st/formfillup/check',
        'Honours/formfillup/check',
        'HSC/formfillup/check',
        'Application/Honours/check',
        'Application/Masters/check',
        'Application/Masters1st/check',
        'Admission/Masters/checkMerit',
        'Admission/Masters1st/checkMerit',
        'Application/Degree/check',
        'Admission/Degree/checkMerit',
        'HSC/2nd/Admission/check',
        'Registration/Masters/Private/check',
        'Registration/Degree/Private/check'
    ];
}
