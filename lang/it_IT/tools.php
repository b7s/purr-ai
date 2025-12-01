<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => 'Fornisci un percorso per continuare.',
        'not_directory' => 'Il percorso ":path" non è una directory.',
        'permission_denied' => 'Non hai i permessi per accedere a ":path".',
        'read_failed' => 'Impossibile leggere il contenuto della directory.',
        'list_summary' => 'Trovati :count di :total elementi in "**:path**"',
        'list_failed' => 'Impossibile elencare il contenuto della directory.',
        'not_found' => 'Il file o la directory ":path" non è stato trovato.',
        'info_retrieved' => 'Informazioni recuperate per ":name".',
        'info_failed' => 'Impossibile ottenere informazioni sul file.',
        'pattern_required' => 'Fornisci un modello di ricerca.',
        'search_summary' => 'Trovati :count risultati per "**:**pattern**" in "**:**path**"',
        'search_failed' => 'Operazione di ricerca non riuscita.',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => 'nome',
            'user_description' => 'descrizione',
            'response_detail' => 'dettaglio risposta',
            'response_tone' => 'tono risposta',
            'timezone' => 'fuso orario',
        ],
        'profile_complete' => 'Ecco le informazioni del tuo profilo, :name.',
        'profile_incomplete' => 'Al tuo profilo mancano: :fields. Vuoi fornire queste informazioni?',
        'profile_updated' => 'Profilo aggiornato con successo: :fields.',
        'errors' => [
            'get_failed' => 'Impossibile recuperare le informazioni del profilo.',
            'update_failed' => 'Impossibile aggiornare il profilo.',
            'no_updates' => 'Nessuna informazione fornita per l’aggiornamento.',
            'validation_failed' => 'Alcuni aggiornamenti non sono riusciti: :errors',
            'name_too_long' => 'Il nome deve avere al massimo 100 caratteri.',
            'description_too_long' => 'La descrizione deve avere al massimo 1000 caratteri.',
            'invalid_detail' => 'Dettaglio risposta non valido. Opzioni valide: :valid',
            'invalid_tone' => 'Tono risposta non valido. Opzioni valide: :valid',
            'invalid_timezone' => 'Fuso orario non valido. Usa il formato IANA (es. Europe/Rome).',
        ],
    ],
];
