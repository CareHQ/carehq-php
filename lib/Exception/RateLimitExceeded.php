<?php

namespace CareHQ\Exception;


class RateLimitExceeded extends APIException
{

    public $doc_str =
        'You have exceeded the number of API requests allowed per second.';

}
