<?php

namespace CareHQ\Exception;


class InvalidRequest extends APIException
{

    public static $doc_str =
        'Not a valid request, most likely a missing or invalid parameter.';

}
