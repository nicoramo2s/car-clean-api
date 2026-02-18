<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {

            if ($request->expectsJson()) {

                $previous = $e->getPrevious();

                if ($previous instanceof ModelNotFoundException) {

                    $model = class_basename($previous->getModel());

                    return response()->json([
                        'success' => false,
                        'message' => "{$model} not found",
                    ], Response::HTTP_NOT_FOUND);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(
            function (ModelNotFoundException $e, $request) {
                if ($request->expectsJson()) {

                    $model = class_basename($e->getModel());
                    $ids = $e->getIds();

                    return response()->json([
                        'success' => false,
                        'message' => "{$model} with id ".implode(',', $ids).' not found',
                    ], Response::HTTP_NOT_FOUND);
                }
            }
        );

        $exceptions->render(function (QueryException $e) {

            if ($e->getCode() === '23000') {

                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate value detected. The record already exists.',
                ], Response::HTTP_CONFLICT);
            }
        });

        $exceptions->render(function (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->isProduction()
                    ? 'Internal server error'
                    : $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
