<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => 'Informe um caminho para continuar.',
        'not_directory' => 'O caminho ":path" não é um diretório.',
        'permission_denied' => 'Você não tem permissão para acessar ":path".',
        'read_failed' => 'Falha ao ler o conteúdo do diretório.',
        'list_summary' => 'Foram encontrados :count de :total itens em "":path""',
        'list_failed' => 'Falha ao listar o conteúdo do diretório.',
        'not_found' => 'O arquivo ou diretório ":path" não foi encontrado.',
        'info_retrieved' => 'Informações obtidas para ":name".',
        'info_failed' => 'Falha ao obter as informações do arquivo.',
        'pattern_required' => 'Informe um padrão de busca.',
        'search_summary' => 'Foram encontrados :count resultados para "":pattern"" em "":path""',
        'search_failed' => 'Falha na operação de busca.',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => 'nome',
            'user_description' => 'descrição',
            'response_detail' => 'detalhe da resposta',
            'response_tone' => 'tom da resposta',
            'timezone' => 'fuso horário',
        ],
        'profile_complete' => 'Aqui estão as informações do seu perfil, :name.',
        'profile_incomplete' => 'Seu perfil está faltando: :fields. Deseja informar esses dados?',
        'profile_updated' => 'Perfil atualizado com sucesso: :fields.',
        'errors' => [
            'get_failed' => 'Falha ao recuperar as informações do perfil.',
            'update_failed' => 'Falha ao atualizar o perfil.',
            'no_updates' => 'Nenhuma informação fornecida para atualização.',
            'validation_failed' => 'Algumas atualizações falharam: :errors',
            'name_too_long' => 'O nome deve ter no máximo 100 caracteres.',
            'description_too_long' => 'A descrição deve ter no máximo 1000 caracteres.',
            'invalid_detail' => 'Detalhe da resposta inválido. Opções válidas: :valid',
            'invalid_tone' => 'Tom de resposta inválido. Opções válidas: :valid',
            'invalid_timezone' => 'Fuso horário inválido. Use o formato IANA (ex.: America/Sao_Paulo).',
        ],
    ],
];
