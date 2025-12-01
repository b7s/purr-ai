<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => 'Indica um caminho para continuar.',
        'not_directory' => 'O caminho ":path" não é um diretório.',
        'permission_denied' => 'Não tens permissão para aceder a ":path".',
        'read_failed' => 'Falha ao ler o conteúdo do diretório.',
        'list_summary' => 'Foram encontrados :count de :total itens em "**:path**"',
        'list_failed' => 'Falha ao listar o conteúdo do diretório.',
        'not_found' => 'O ficheiro ou diretório ":path" não foi encontrado.',
        'info_retrieved' => 'Informações obtidas para ":name".',
        'info_failed' => 'Falha ao obter informações do ficheiro.',
        'pattern_required' => 'Indica um padrão de pesquisa.',
        'search_summary' => 'Foram encontrados :count resultados para "**:**pattern**" em "**:**path**"',
        'search_failed' => 'Falha na operação de pesquisa.',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => 'nome',
            'user_description' => 'descrição',
            'response_detail' => 'detalhe da resposta',
            'response_tone' => 'tom da resposta',
            'timezone' => 'fuso horário',
        ],
        'profile_complete' => 'Aqui está a tua informação de perfil, :name.',
        'profile_incomplete' => 'O teu perfil está em falta: :fields. Gostarias de fornecer estes dados?',
        'profile_updated' => 'Perfil atualizado com sucesso: :fields.',
        'errors' => [
            'get_failed' => 'Falha ao obter informações do perfil.',
            'update_failed' => 'Falha ao atualizar o perfil.',
            'no_updates' => 'Não foi fornecida informação para atualizar.',
            'validation_failed' => 'Algumas atualizações falharam: :errors',
            'name_too_long' => 'O nome deve ter no máximo 100 caracteres.',
            'description_too_long' => 'A descrição deve ter no máximo 1000 caracteres.',
            'invalid_detail' => 'Detalhe de resposta inválido. Opções válidas: :valid',
            'invalid_tone' => 'Tom de resposta inválido. Opções válidas: :valid',
            'invalid_timezone' => 'Fuso horário inválido. Usa o formato IANA (ex.: Europe/Lisbon).',
        ],
    ],
];
