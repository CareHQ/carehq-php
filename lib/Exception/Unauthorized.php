<?php

namespace CareHQ\Exception;


class Unauthorized extends APIException
{

    public static $doc_str = 'The API credentials provided are not valid.';

}
