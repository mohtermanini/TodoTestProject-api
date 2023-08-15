<?php

use Illuminate\Validation\Rules\Password;

return [
    'password' => Password::min(8)->mixedCase()
];