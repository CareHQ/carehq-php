<?php

namespace CareHQ\Exception;


class InvalidRequest extends APIException
{

    private $doc_str =
        'Not a valid request, most likely a missing or invalid parameter.';

}
