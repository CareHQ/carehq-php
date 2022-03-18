<?php

namespace CareHQ\Exception;


class NotFound extends APIException
{

    public static $doc_str =
        'The endpoint you are calling or the document you referenced ' .
        'doesn\'t exist.';

}
