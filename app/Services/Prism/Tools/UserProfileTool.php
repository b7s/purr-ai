<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Tool;

class UserProfileTool
{
    public static function make(): Tool
    {
        $validTones = collect(config('purrai.response_tones', []))
            ->pluck('value')
            ->toArray();

        return (new Tool)
            ->as('user_profile')
            ->for(
                'Manage user profile settings and preferences. Use this tool to get or update user information like name, description, response preferences, and timezone. '.
                'When the user asks about their profile or wants to update their preferences, use this tool. '.
                'If user information is missing and needed, ask the user to provide it.'
            )
            ->withParameter(new EnumSchema(
                'action',
                'The action to perform',
                ['get', 'update']
            ), required: true)
            ->withParameter(new StringSchema(
                'user_name',
                'The user\'s name (for update action)'
            ), required: false)
            ->withParameter(new StringSchema(
                'user_description',
                'A brief description about the user - interests, profession, preferences (for update action)'
            ), required: false)
            ->withParameter(new EnumSchema(
                'response_detail',
                'Preferred response detail level',
                ['detailed', 'short']
            ), required: false)
            ->withParameter(new EnumSchema(
                'response_tone',
                'Preferred response tone style',
                $validTones
            ), required: false)
            ->withParameter(new StringSchema(
                'timezone',
                'User timezone in IANA format (e.g., America/New_York, Europe/London, Asia/Tokyo)'
            ), required: false)
            ->using(function (
                string $action,
                ?string $user_name = null,
                ?string $user_description = null,
                ?string $response_detail = null,
                ?string $response_tone = null,
                ?string $timezone = null
            ): string {
                $handler = new UserProfileToolHandler;

                return match ($action) {
                    'get' => $handler->getProfile(),
                    'update' => $handler->updateProfile($user_name, $user_description, $response_detail, $response_tone, $timezone),
                    default => json_encode(['error' => "Action [$action] not found"]),
                };
            });
    }
}
