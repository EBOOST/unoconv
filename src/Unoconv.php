<?php
/**
 * Created by Eboost Interactive BV.
 * User: Bert van Hoekelen
 * Date: 16/03/16
 */

namespace Eboost\Unoconv;

use Illuminate\Filesystem\FilesystemManager;
use Eboost\Unoconv\Transport\AbstractTransport;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Queue\QueueManager;

class Unoconv
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var AbstractTransport
     */
    protected $transport;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var ConvertFile
     */
    protected $input;

    /**
     * @var ConvertFile
     */
    protected $output;

    /**
     * Laravels Queue Manager.
     *
     * @var QueueManager
     */
    protected $queue;

    /**
     * Dispatch a Job after the conversion is done.
     *
     * @var null
     */
    protected $after;

    /**
     * Unoconv constructor.
     *
     * @param array $config
     * @param FilesystemManager $filesystem
     */
    public function __construct($config = [], FilesystemManager $filesystem = null, QueueManager $queue)
    {
        $this->setTransport($config);
        $this->setFilesystem($filesystem->disk());

        $this->queue = $queue;
    }

    /**
     * The file that needs to be converted.
     *
     * @param string $file
     * @return $this
     */
    public function file(string $file)
    {
        $this->init($file);

        return $this;
    }

    /**
     * Convert the file to given types.
     *
     * @param array|string $types
     * @return $this
     */
    public function to($types)
    {
        if (is_string($types)) {
            $types = func_get_args();
        }

        $this->convert($types);
        $this->start();

        return $this;
    }

    /**
     * Set the transport engine.
     *
     * @param $config
     * @throws \Exception
     */
    public function setTransport($config)
    {
        $this->transport = AbstractTransport::create($config['transport'], $config);
    }

    /**
     * Set filesystem.
     *
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Convert the file on a specific queue.
     *
     * @param bool|string $queueName
     * @param array $types
     * @return $this
     */
    public function onQueue($queueName, array $types)
    {
        $this->convert($types);
        $this->start($queueName);

        return $this;
    }

    /**
     * Queue the conversion.
     *
     * @param array $types
     * @return $this
     */
    public function queue(array $types)
    {
        $this->onQueue(true, $types);

        return $this;
    }

    /**
     * Dispatch another job after conversion is done.
     *
     * @param $after
     * @return $this
     */
    public function after($after)
    {
        $this->after = $after;

        return $this;
    }

    /**
     * Create in/output files.
     *
     * @param null $resource
     * @return ConvertFile
     * @throws \Exception
     */
    protected function init($resource = null)
    {
        if (empty($this->resource) && !empty($resource)) {
            $this->resource = $resource;
        }

        if ($this->isReadable()) {
            return $this->initInput();
        }

        throw new \Exception('File input is not readable');
    }

    /**
     * Convert the types.
     *
     * @param array $types
     * @throws \Exception
     */
    protected function convert(array $types)
    {
        $this->checkForInput();

        foreach ($types as $type) {
            $this->initOutput($type);
        }
    }

    /**
     * Check if resource is readable.
     *
     * @return bool
     */
    protected function isReadable()
    {
        if (is_string($this->resource)) {
            return isset($this->filesystem) && $this->filesystem->exists($this->resource);
        }

        return false;
    }

    /**
     * Create input file.
     *
     * @return ConvertFile
     */
    protected function initInput()
    {
        return $this->input = new ConvertFile($this->resource, $this->filesystem);
    }

    /**
     * Create output file.
     *
     * @param $type
     */
    protected function initOutput($type)
    {
        $this->output = new ConvertFile($type, $this->filesystem);
        $this->output->filenameCheck($this->input);
    }

    /**
     * Start converting files.
     *
     * @param null $queue
     */
    protected function start($queue = null)
    {
        $job = new Jobs\ConvertFile($this->transport, $this->input, $this->output, $this->after);

        if (isset($queue)) {
            $this->queue->connection()->pushOn(is_string($queue) ? $queue : null, $job);

            return;
        }

        $job->handle();
    }

    /**
     * @throws \Exception
     */
    private function checkForInput()
    {
        if (!$this->input) {
            throw new \Exception('Please set the file before converting');
        }
    }
}
