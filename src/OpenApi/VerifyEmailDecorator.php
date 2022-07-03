<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

class VerifyEmailDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    ){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);


        $pathItem = new Model\PathItem(
            ref: "Verify user's email",
            post: new Model\Operation(
                operationId: 'get',
                tags: ['User'],
                responses: [
                    '200' => [
                        'description' => "Your email address has been confirmed.",
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
                    'dynamic' => [
                        'description' => "Error.",
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
                summary: "Verify user's email",

            ),
        );
        $openApi->getPaths()->addPath('/api/verify_email/{id}', $pathItem);

        return $openApi;
    }


}