<?php

/**
 * Class DataStore
 */
class DataStore
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $storage;

    private $filename;

    public function __construct($filename = null)
    {
        $this->storage = app('filesystem');

        $this->filename = $filename;
    }

    /**
     * Get all data from storage
     * @return array|null
     */
    public function getAllData()
    {
        if (!$this->storage->has($this->filename)) return null;

        return unserialize($this->storage->get($this->filename));
    }

    /**
     * Adding values to section
     *
     * @param $name
     * @param $value
     * @return bool
     */
    public function add($name, $value)
    {
        $data = $this->getAllData() ?? [];

        if (isset($data[$name])) {
            if (!empty($data[$name]) && is_array($data[$name])) {
                $data[$name][] = $value;
            } else {
                $data[$name] += $value;
            }
        } else {
            $data[$name] = [$value];
        }

        return $this->storage->put($this->filename, serialize($data));
    }

    /**
     * Get data from section
     *
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        $data = $this->getAllData();

        if (!array_has($data, $name)) return null;

        return array_get($data, $name);
    }

    /**
     * Check is exists value in section
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return !is_null($this->get($name));
    }

    /**
     * Remove file with data
     * @return bool
     */
    public function flush()
    {
        return $this->storage->delete($this->filename);
    }
}