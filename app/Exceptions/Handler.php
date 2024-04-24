<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // List of exception types that should not be logged
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Optional: Log or handle specific exceptions here
        });

        $this->renderable(function (Throwable $e, $request) {
            return $this->render($request, $e);
        });
    }

    /**
     * Handle API exceptions, ensuring JSON responses for API requests.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response
     */

    public function render($request, Throwable $exception)
    {
        // Check if the request wants a JSON response or is an API request
        // if ($request->wantsJson() || $request->is('api/*')) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Server Error', // Customize the message or use $exception->getMessage() for detail
        //         'error' => $exception->getMessage() . ' - Test Word',
        //     ], 500);
        // }

        // Default to the parent method's handling, which renders HTML for web routes
        return parent::render($request, $exception);
    }
    public function report(Throwable $exception)
    {
        if (app()->runningInConsole() && app()->environment('production')) {
            $logger = Log::channel('api');
            $logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        parent::report($exception);
    }
}
