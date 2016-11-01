<?php
/**
 * Created by Eboost Interactive BV.
 * User: Bert van Hoekelen
 * Date: 16/03/16
 */

namespace Eboost\Unoconv;

use Illuminate\Contracts\Filesystem\Filesystem;

class ConvertFile
{
    /**
     * The format of the file.
     *
     * @var string
     */
    protected $format;

    /**
     * Only the file name.
     *
     * @var string
     */
    protected $filename;

    /**
     * The path of the file.
     *
     * @var string
     */
    protected $path;

    /**
     * The contents of the file.
     *
     * @var string
     */
    protected $data;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct($file, Filesystem $filesystem)
    {
        $this->setFile($file);
        $this->filesystem = $filesystem;

        $this->setPath(dirname($file));
    }

    /**
     * Save new data to the file.
     *
     * @param $contents
     * @return bool
     */
    public function save($contents)
    {
        return $this->filesystem->put($this->getFileName(), $contents);
    }

    /**
     * Get the content of the file.
     *
     * @return string
     */
    public function get()
    {
        return $this->filesystem->get($this->getFilePath());
    }

    /**
     * Check the filename, if there is no filename and path present it will use the same filename as the input.
     *
     * @param ConvertFile $input
     */
    public function filenameCheck(ConvertFile $input)
    {
        if (empty($this->getFilename()) && empty($this->getPath())) {
            $this->setFilename($input->getFilename(), $this->getFormat());
        }
    }

    /**
     * Get the format of of the file.
     *
     * @return string
     * @throws \Exception
     */
    public function getFormat()
    {
        if (is_null($this->format)) {
            $this->format = $this->getExtension();
        }

        return $this->format;
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function getFilePath()
    {
        if (!empty($this->getPath())) {
            return $this->getPath() . $this->getFilename();
        }

        return $this->getFilename();
    }

    /**
     * Get the extension based on the filename.
     *
     * @return string
     * @throws \Exception
     */
    public function getExtension()
    {
        return $this->parseExtension($this->filename);
    }

    /**
     * Parse extension of the filename.
     *
     * @param $filename
     * @return mixed|string
     */
    public function parseExtension($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $ext = preg_replace("/(\?.*)/i", '', $ext);

        return $ext;
    }

    /**
     * Set the filename.
     *
     * @param $filename
     * @param null|string $ext
     *
     * @return mixed
     */
    public function setFilename($filename, $ext = null)
    {
        if (is_null($ext)) {
            return $this->filename = $filename;
        }

        return $this->filename = preg_replace("/{$this->parseExtension($filename)}$/", $ext, $filename);
    }

    /**
     * Get the filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the file path.
     *
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        if ($path != '.') {
            $this->path = $path;
        }

        return $this;
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the file.
     * This also checks if the given value is only an format.
     *
     * @param $file
     * @return $this
     */
    protected function setFile($file)
    {
        if ($this->isFormat($file)) {
            $this->format = $file;

            return $this;
        }

        $this->setFilename(basename($file));

        return $this;
    }

    /**
     * Check if the given file is an format.
     *
     * @param $file
     * @return bool
     */
    protected function isFormat($file)
    {
        return ctype_alnum($file);
    }
}
