<?php

namespace CareHQ\Exception;


class RateLimitExceeded extends APIException
{

    private $doc_str =
        'You have exceeded the number of API requests allowed per second.';

}
