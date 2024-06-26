<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExceptionOccured; // This should be your custom Mailable class


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
    // Check if the request is targeting an API route
    if ($request->is('api/*')) {
        // Get the route information if available
        $routeName = null;
        $routeAction = null;
        if ($request->route()) {
            $routeName = $request->route()->getName();
            $routeAction = $request->route()->getAction();
        }

        // Prepare basic log data
        $logData = [
            'exception' => $exception,
            'retryAfter' => null,  // Default to null if no Retry-After is available
            'route' => [
                'name' => $routeName,
                'action' => $routeAction
            ],
            'file' => $exception->getFile()
        ];

        // Log all exceptions with detailed information
        $logger = Log::channel('api');
        $logger->error($exception->getMessage(), $logData);
        try {
            // Mail::to('ib.farag@gmail.com')->send(new \App\Mail\ExceptionOccurred($exception));
        } catch (\Exception $mailException) {
            // Handle mail sending error
            $logger->error('Failed to send exception email', ['error' => $mailException->getMessage()]);
        }

    }

    // Default to the parent method's handling, which renders HTML for web routes
    return parent::render($request, $exception);
}

         public function report(Throwable $exception)
    {
        if (app()->runningInConsole() && app()->environment('local')) {
            $logger = Log::channel('api');
            $logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        parent::report($exception);
    }
}
