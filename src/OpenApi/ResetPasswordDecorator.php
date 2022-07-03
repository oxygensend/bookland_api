<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class ResetPasswordDecorator implements  OpenApiFactoryInterface
{

    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    ){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);


        $pathItem = new Model\PathItem(
            ref: 'Reset Password',
            post: new Model\Operation(
                operationId: 'post',
                tags: ['User'],
                responses: [
                    '200' => [
                        'description' => "Reset user's password",
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'new_password' => [
                                            'type' => 'string',
                                            'readOnly' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: "Reset user's password",

            ),
        );
        $openApi->getPaths()->addPath('/api/reset_password/{id}', $pathItem);

        return $openApi;
    }


}