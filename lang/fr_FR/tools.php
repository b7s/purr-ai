<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => 'Fournis un chemin pour continuer.',
        'not_directory' => 'Le chemin « :path » n’est pas un dossier.',
        'permission_denied' => 'Tu n’as pas l’autorisation d’accéder à « :path ».',
        'read_failed' => 'Impossible de lire le contenu du dossier.',
        'list_summary' => ':count élément(s) sur :total trouvés dans « **:path** »',
        'list_failed' => 'Impossible de lister le contenu du dossier.',
        'not_found' => 'Le fichier ou dossier « :path » est introuvable.',
        'info_retrieved' => 'Informations récupérées pour « :name ».',
        'info_failed' => 'Impossible de récupérer les informations du fichier.',
        'pattern_required' => 'Fournis un motif de recherche.',
        'search_summary' => ':count résultat(s) pour « **:**pattern** » dans « **:**path** »',
        'search_failed' => 'La recherche a échoué.',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => 'nom',
            'user_description' => 'description',
            'response_detail' => 'niveau de détail des réponses',
            'response_tone' => 'ton des réponses',
            'timezone' => 'fuseau horaire',
        ],
        'profile_complete' => 'Voici les informations de ton profil, :name.',
        'profile_incomplete' => 'Ton profil n’a pas les éléments suivants : :fields. Veux-tu les fournir ?',
        'profile_updated' => 'Profil mis à jour avec succès : :fields.',
        'errors' => [
            'get_failed' => 'Impossible de récupérer les informations du profil.',
            'update_failed' => 'Impossible de mettre à jour le profil.',
            'no_updates' => 'Aucune information à mettre à jour.',
            'validation_failed' => 'Certaines mises à jour ont échoué : :errors',
            'name_too_long' => 'Le nom doit contenir 100 caractères ou moins.',
            'description_too_long' => 'La description doit contenir 1000 caractères ou moins.',
            'invalid_detail' => 'Niveau de détail invalide. Options valides : :valid',
            'invalid_tone' => 'Ton invalide. Options valides : :valid',
            'invalid_timezone' => 'Fuseau horaire invalide. Utilise le format IANA (ex. : Europe/Paris).',
        ],
    ],
];
