<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

class ResendVerificationTokenDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    ){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);


        $pathItem = new Model\PathItem(
            ref: 'Resend verification token',
            post: new Model\Operation(
                operationId: 'post',
                tags: ['User'],
                responses: [
                    '200' => [
                        'description' => "Resend verification token",
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'success' => [
                                            'type' => 'string',
                                            'readOnly' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                '401' => [
                    'description' => "Your email address has been confirmed.",
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'error' => [
                                        'type' => 'string',
                                        'readOnly' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                ],
                summary: "Resend verification token",

            ),
        );
        $openApi->getPaths()->addPath('/api/resend_token/{id}', $pathItem);

        return $openApi;
    }

}