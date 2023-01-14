<?php

declare(strict_types=1);

namespace Waldhacker\Pseudify\Encoder;

use Waldhacker\Pseudify\Core\Processor\Encoder\EncoderInterface;

class Rot13Encoder implements EncoderInterface
{
    private array $defaultContext = [];

    /**
     * @api
     */
    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * @param string $data
     *
     * @return string|false
     *
     * @api
     */
    public function decode($data, array $context = [])
    {
        return @str_rot13($data);
    }

    /**
     * @param string $data
     *
     * @return string
     *
     * @api
     */
    public function encode($data, array $context = [])
    {
        return @str_rot13($data);
    }
}
