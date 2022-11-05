<?php

return [

    'default_auth_profile' => env('GOOGLE_CALENDAR_AUTH_PROFILE', 'service_account'),

    'auth_profiles' => [

        /*
         * Autenticarse usando una cuenta de servicio.
         */
        'service_account' => [
            /*
             * Ruta al archivo json que contiene las credenciales.
             */
            'credentials_json' => storage_path('app/google-calendar/service-account-credentials.json'),
        ],

        /*
         * Autenticar con la cuenta de usuario real de Google.
         */
        'oauth' => [
            /*
             * Ruta al archivo json que contiene las credenciales de oauth2.
             */
            'credentials_json' => storage_path('app/google-calendar/oauth-credentials.json'),

            /*
            * Ruta al archivo json que contiene el token oauth2.
             */
            'token_json' => storage_path('app/google-calendar/oauth-token.json'),
        ],
    ],

    /*
     * El id del Google Calendar que se usará por defecto.
     */
    'calendar_id' => env('nietojr1@gmail.com'),

     /*
      * La dirección de correo electrónico de la cuenta de usuario a suplantar.
     */
    'user_to_impersonate' => env('nietojr1@gmail.com'),
];
