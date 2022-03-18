<?php

namespace CareHQ\Exception;


/**
 * Base API exception.
 */
class APIException extends \Exception
{

    public static $doc_str =
        'An error occurred while processing an API the ' .
        'request.';

    // The status code associated with the error
    public $status_code;

    // A hint providing additional information as to why this error occurred.
    public $hint;

    // A dictionary of errors relating to the arguments (parameters) sent
    // to the API endpoint (e.g `{'arg_name': ['error1', ...]}`).
    public $arg_errors;

    public function __construct($status_code, $hint=NULL, $arg_errors=NULL) {
        parent::__construct();

        $this->status_code = $status_code;
        $this->hint = $hint;
        $this->arg_errors = $arg_errors;
    }

    public function __toString() {
        $parts = ['[' . strval($this->status_code) . '] ' . self::$doc_str];

        if ($this->hint) {
            array_push($parts, 'Hint: ' . $this->hint);
        }

        if ($this->arg_errors) {
            array_push(
                'Argument errors:\n- ' . join(
                    '\n- ',
                    array_map(
                        function ($arg, $errors) {
                            return $arg . ': ' . join(' ', $errors);
                        },
                        $this->arg_errors
                    )
                )
            );
        }

        return join('\n---\n', $parts);
    }

    public static function get_class_by_status_code(
        $error_type,
        $default=NULL
    ) {
        $class_map = [
            400=>InvalidRequest::class,
            401=>Unauthorized::class,
            403=>Forbidden::class,
            405=>Forbidden::class,
            404=>NotFound::class,
            429=>RateLimitExceeded::class
        ];

        return in_array($error_type, $class_map)
            ? $class_map[$error_type]
            : $default ? $default : APIException::class;
    }
}




