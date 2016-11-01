<?php
/**
 * Created by Eboost Interactive BV.
 * User: Bert van Hoekelen
 * Date: 15/03/16
 */

namespace Eboost\Unoconv\Transport;


use Eboost\Unoconv\ConvertFile;

abstract class AbstractTransport
{
    /**
     * @param ConvertFile $input
     * @param ConvertFile $output
     */
    abstract public function convert($input, $output);

    /**
     * @param $transport
     * @param $config
     * @return AbstractTransport|null
     * @throws \Exception
     */
    public static function create($transport, $config)
    {
        if (is_string($transport)) {
            $className = 'Eboost\\Unoconv\\Transport\\' . $transport;

            if (!class_exists($className)) {
                throw new \Exception('Invalid transport');
            }

            return new $className($config);
        }

        return null;
    }
}
