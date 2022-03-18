<?php

namespace CareHQ\Exception;


class Unauthorized extends APIException
{

    private $doc_str = 'The API credentials provided are not valid.';

}
