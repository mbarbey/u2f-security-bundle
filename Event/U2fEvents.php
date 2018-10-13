<?php

namespace Mbarbey\U2fSecurityBundle\Event;

class U2fEvents
{
    const U2F_PRE_REGISTRATION = 'u2f.registration.before';

    const U2F_REGISTRATION_SUCCESS = 'u2f.registration.success';

    const U2F_REGISTRATION_FAILURE = 'u2f.registration.failure';

    const U2F_POST_REGISTRATION = 'u2f.registration.after';



    const U2F_PRE_AUTHENTICATION = 'u2f.authentication.before';

    const U2F_AUTHENTICATION_REQUIRED = 'u2f.authentication.required';

    const U2F_AUTHENTICATION_SUCCESS = 'u2f.authentication.success';

    const U2F_AUTHENTICATION_FAILURE = 'u2f.authentication.failure';

    const U2F_POST_AUTHENTICATION = 'u2f.authentication.after';
}