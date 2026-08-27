<?php

return [
    /*
     * Secret partagé (HMAC) transmis par le dashboard Patchub à la création de l'application.
     * Doit correspondre exactement au "webhook_secret" stocké côté dashboard.
     */
    'webhook_secret' => env('PATCHUB_WEBHOOK_SECRET'),

    /*
     * Chemin sur lequel le package écoute les webhooks entrants du dashboard.
     */
    'webhook_path' => env('PATCHUB_WEBHOOK_PATH', 'patchub/webhook'),
];
