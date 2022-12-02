<?php

namespace Stichoza\NbgCurrency;

use BadMethodCallException;
use Carbon\Carbon;
use ErrorException;
use stdClass;
use Throwable;

/**
 * NBG currency service wrapper class
 *
 * @author      Levan Velijanashvili <me@stichoza.com>
 * @link        http://stichoza.com/
 * @license     MIT
 */
class NbgCurrency
{
    /**
     * @var \Stichoza\NbgCurrency\Data\Currencies[]
     */
    protected static array $data = [];

    /**
     * @var string JSON URL
     */
    protected string $url = 'https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/ka/json';



}
