<?php

namespace CareHQ\Exception;


class NotFound extends APIException
{

    private $doc_str =
        'The endpoint you are calling or the document you referenced ' .
        'doesn\'t exist.';

}
