<?php
/**
 * Created by Eboost Interactive BV.
 * User: Bert van Hoekelen
 * Date: 15/03/16
 */

namespace Eboost\Unoconv\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class Guzzle extends AbstractTransport
{
    /**
     * The guzzle client used for the requests.
     *
     * @var Client
     */
    protected $guzzleClient;

    /**
     * The url used for the unoconv service.
     *
     * @var string
     */
    protected $url;

    /**
     * The path of the unoconv service.
     */
    protected $path;

    /**
     * Guzzle constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->url = $this->getBaseUrl($config);
        $this->path = $config['path'] . '/';
    }

    /**
     * Execute the convert command.
     *
     * @param \Eboost\Unoconv\ConvertFile $input
     * @param \Eboost\Unoconv\ConvertFile $output
     */
    public function convert($input, $output)
    {
        $body = [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $input->get(),
                    'filename' => $input->getFilePath()
                ]
            ]
        ];

        $res = $this->getGuzzleClient()->request('POST', $this->path . $output->getFormat(), $body);

        $output->save($res->getBody()->getContents());
    }

    /**
     * Get guzzle client.
     *
     * @return Client
     */
    protected function getGuzzleClient()
    {
        if (!$this->guzzleClient) {
            $this->guzzleClient = new Client([
                'base_uri' => $this->url
            ]);
        }

        return $this->guzzleClient;
    }

    /**
     * Create base url from config variables.
     *
     * @param $config
     * @return string
     */
    protected function getBaseUrl($config)
    {
        $url = (string) Uri::fromParts([
            'scheme' => $config['scheme'],
            'host' => $config['host'],
            'port' => $config['port'],
            'path' => ltrim($config['path'], '/')
        ]);

        return rtrim($url, '/');
    }
}
