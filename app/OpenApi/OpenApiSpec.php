<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'CarClean API',
    description: 'Documentacion oficial de la API de CarClean'
)]
#[OA\Server(
    url: '/',
    description: 'Servidor principal'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Usa: Bearer {token}'
)]
#[OA\Schema(
    schema: 'ClientResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Juan Perez'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan@example.com'),
        new OA\Property(property: 'phone', type: 'string', example: '+5491122334455'),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'VehicleResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'client_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'brand', type: 'string', example: 'Toyota'),
        new OA\Property(property: 'model', type: 'string', example: 'Corolla'),
        new OA\Property(property: 'year', type: 'integer', nullable: true, example: 2024),
        new OA\Property(property: 'color', type: 'string', nullable: true, example: 'Blanco'),
        new OA\Property(property: 'license_plate', type: 'string', example: 'AA123BB'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'ServiceResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Lavado Premium'),
        new OA\Property(property: 'description', type: 'string', example: 'Lavado interior y exterior'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 25000),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'SaleResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'client_id', type: 'integer', example: 1),
        new OA\Property(property: 'vehicle_id', type: 'integer', example: 2),
        new OA\Property(property: 'subtotal', type: 'number', format: 'float', example: 35000),
        new OA\Property(property: 'total', type: 'number', format: 'float', example: 35000),
        new OA\Property(property: 'payment_method', type: 'string', example: 'cash'),
        new OA\Property(property: 'paid_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'should_invoice', type: 'boolean', example: false),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class OpenApiSpec
{
    #[OA\Post(
        path: '/api/v1/register',
        summary: 'Register a new user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User registered successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(): void {}

    #[OA\Post(
        path: '/api/v1/login',
        summary: 'Login user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function login(): void {}

    #[OA\Post(
        path: '/api/v1/logout',
        summary: 'Logout user',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logout successful'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(): void {}

    #[OA\Get(
        path: '/api/v1/clients',
        summary: 'List clients',
        tags: ['Clients'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50)),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Clients retrieved successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function clientsIndex(): void {}

    #[OA\Post(
        path: '/api/v1/clients',
        summary: 'Create client',
        tags: ['Clients'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Juan Perez'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan@example.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '+5491122334455'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Client created successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function clientsStore(): void {}

    #[OA\Get(
        path: '/api/v1/clients/{client}',
        summary: 'Get client by ID',
        tags: ['Clients'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'client', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Client retrieved successfully'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function clientsShow(): void {}

    #[OA\Put(
        path: '/api/v1/clients/{client}',
        summary: 'Update client',
        tags: ['Clients'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'client', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Juan Perez'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan@example.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '+5491122334455'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Client updated successfully'),
            new OA\Response(response: 404, description: 'Client not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function clientsUpdate(): void {}

    #[OA\Delete(
        path: '/api/v1/clients/{client}',
        summary: 'Delete client',
        tags: ['Clients'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'client', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Client deleted successfully'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function clientsDestroy(): void {}

    #[OA\Get(
        path: '/api/v1/vehicles',
        summary: 'List vehicles',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50)),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Vehicles retrieved successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function vehiclesIndex(): void {}

    #[OA\Post(
        path: '/api/v1/vehicles',
        summary: 'Create vehicle',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['client_id', 'brand', 'model', 'license_plate'],
                properties: [
                    new OA\Property(property: 'client_id', type: 'integer', example: 1),
                    new OA\Property(property: 'brand', type: 'string', example: 'Toyota'),
                    new OA\Property(property: 'model', type: 'string', example: 'Corolla'),
                    new OA\Property(property: 'year', type: 'integer', nullable: true, example: 2024),
                    new OA\Property(property: 'color', type: 'string', nullable: true, example: 'Blanco'),
                    new OA\Property(property: 'license_plate', type: 'string', example: 'AA123BB'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Vehicle created successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function vehiclesStore(): void {}

    #[OA\Get(
        path: '/api/v1/vehicles/{vehicle}',
        summary: 'Get vehicle by ID',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'vehicle', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Vehicle retrieved successfully'),
            new OA\Response(response: 404, description: 'Vehicle not found'),
        ]
    )]
    public function vehiclesShow(): void {}

    #[OA\Put(
        path: '/api/v1/vehicles/{vehicle}',
        summary: 'Update vehicle',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'vehicle', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['brand', 'model'],
                properties: [
                    new OA\Property(property: 'brand', type: 'string', example: 'Honda'),
                    new OA\Property(property: 'model', type: 'string', example: 'Civic'),
                    new OA\Property(property: 'year', type: 'integer', nullable: true, example: 2022),
                    new OA\Property(property: 'color', type: 'string', nullable: true, example: 'Negro'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Vehicle updated successfully'),
            new OA\Response(response: 404, description: 'Vehicle not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function vehiclesUpdate(): void {}

    #[OA\Delete(
        path: '/api/v1/vehicles/{vehicle}',
        summary: 'Delete vehicle',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'vehicle', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Vehicle deleted successfully'),
            new OA\Response(response: 404, description: 'Vehicle not found'),
        ]
    )]
    public function vehiclesDestroy(): void {}

    #[OA\Get(
        path: '/api/v1/services',
        summary: 'List services',
        tags: ['Services'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'price', in: 'query', schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'description', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Services retrieved successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function servicesIndex(): void {}

    #[OA\Post(
        path: '/api/v1/services',
        summary: 'Create service',
        tags: ['Services'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'description'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Lavado Premium'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 25000),
                    new OA\Property(property: 'description', type: 'string', example: 'Lavado interior y exterior'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Service created successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function servicesStore(): void {}

    #[OA\Get(
        path: '/api/v1/services/{service}',
        summary: 'Get service by ID',
        tags: ['Services'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'service', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Service retrieved successfully'),
            new OA\Response(response: 404, description: 'Service not found'),
        ]
    )]
    public function servicesShow(): void {}

    #[OA\Put(
        path: '/api/v1/services/{service}',
        summary: 'Update service',
        tags: ['Services'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'service', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', nullable: true, example: 'Lavado Full'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', nullable: true, example: 35000),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Lavado completo'),
                    new OA\Property(property: 'active', type: 'boolean', nullable: true, example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Service updated successfully'),
            new OA\Response(response: 404, description: 'Service not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function servicesUpdate(): void {}

    #[OA\Delete(
        path: '/api/v1/services/{service}',
        summary: 'Delete service',
        tags: ['Services'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'service', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Service deleted successfully'),
            new OA\Response(response: 404, description: 'Service not found'),
        ]
    )]
    public function servicesDestroy(): void {}

    #[OA\Get(
        path: '/api/v1/sales',
        summary: 'List sales',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'client_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'vehicle_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'payment_method', in: 'query', schema: new OA\Schema(type: 'string', enum: ['cash', 'transfer', 'card'])),
            new OA\Parameter(name: 'should_invoice', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100)),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sales retrieved successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function salesIndex(): void {}

    #[OA\Post(
        path: '/api/v1/sales',
        summary: 'Create sale',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['client_id', 'vehicle_id', 'payment_method', 'should_invoice', 'items'],
                properties: [
                    new OA\Property(property: 'client_id', type: 'integer', example: 1),
                    new OA\Property(property: 'vehicle_id', type: 'integer', example: 1),
                    new OA\Property(property: 'payment_method', type: 'string', enum: ['cash', 'transfer', 'card'], example: 'cash'),
                    new OA\Property(property: 'paid_at', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'should_invoice', type: 'boolean', example: false),
                    new OA\Property(property: 'point_of_sale', type: 'integer', nullable: true, example: 1),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['service_id'],
                            properties: [
                                new OA\Property(property: 'service_id', type: 'integer', example: 3),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Sale created successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function salesStore(): void {}

    #[OA\Get(
        path: '/api/v1/sales/{sale}',
        summary: 'Get sale by ID',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'sale', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sale retrieved successfully'),
            new OA\Response(response: 404, description: 'Sale not found'),
        ]
    )]
    public function salesShow(): void {}

    #[OA\Put(
        path: '/api/v1/sales/{sale}',
        summary: 'Update sale',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'sale', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['client_id', 'vehicle_id', 'payment_method', 'should_invoice', 'items'],
                properties: [
                    new OA\Property(property: 'client_id', type: 'integer', example: 1),
                    new OA\Property(property: 'vehicle_id', type: 'integer', example: 1),
                    new OA\Property(property: 'payment_method', type: 'string', enum: ['cash', 'transfer', 'card'], example: 'card'),
                    new OA\Property(property: 'paid_at', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'should_invoice', type: 'boolean', example: true),
                    new OA\Property(property: 'point_of_sale', type: 'integer', nullable: true, example: 1),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['service_id'],
                            properties: [
                                new OA\Property(property: 'service_id', type: 'integer', example: 3),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Sale updated successfully'),
            new OA\Response(response: 404, description: 'Sale not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function salesUpdate(): void {}

    #[OA\Delete(
        path: '/api/v1/sales/{sale}',
        summary: 'Delete sale',
        tags: ['Sales'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'sale', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Sale deleted successfully'),
            new OA\Response(response: 404, description: 'Sale not found'),
        ]
    )]
    public function salesDestroy(): void {}
}
