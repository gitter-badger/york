<?php
namespace York\Request;

/**
 * provides all sent data as object access
 *
 * @package \York\Request
 * @version $version$
 * @author wolxXx
 */
class Data
{
    /**
     * the main data storage
     *
     * @var array
     */
    private $data;

    /**
     * storage for file-array
     *
     * @var array
     */
    private $files;

    /**
     * set of York\Request\File instances
     *
     * @var \York\Request\File[]
     */
    private $fileObjects;

    /**
     * a save of the original GET-array
     *
     * @var array
     */
    private $rawGET;

    /**
     * a save of the original POST-array
     *
     * @var array
     */
    private $rawPOST;

    /**
     * a save of the original FILES-array
     *
     * @var array
     */
    private $rawFILES;

    /**
     * instantiates all arrays, starts scanning the data
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * initialises the data object
     *
     * @return \York\Request\Data
     */
    protected function init()
    {
        $this->data = array();
        $this->files = array();
        $this->fileObjects = array();
        $this->rawPOST = $_POST;
        $this->rawGET = $_GET;
        $this->rawFILES = $_FILES;
        $this
            ->scanGetData()
            ->scanPostData()
            ->scanFiles()
        ;

        return $this;
    }

    /**
     * setter for data
     *
     * @param string    $key
     * @param mixed     $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * set data from an array
     *
     * @param array $array
     *
     * @return \York\Request\Data
     */
    protected function setFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * scans the post array and saves the data
     *
     * @return $this
     */
    protected function scanPostData()
    {
        foreach ($this->rawPOST as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * scans the get array and saves the data
     *
     * @return $this
     */
    protected function scanGetData()
    {
        foreach ($this->rawGET as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * scans the files and saves all files as new FileUploadObjects
     *
     * @return $this
     */
    protected function scanFiles()
    {
        foreach ($this->rawFILES as $key => $value) {
            $this->files[$key] = $value;

            if (is_array($value['name'])) {
                foreach (array_keys($value['name']) as $index) {
                    if ('' === $value['name'][$index]) {
                        continue;
                    }

                    $this->fileObjects[] = new \York\Request\File($value['name'][$index], $value['type'][$index], $value['tmp_name'][$index], $value['error'][$index], $value['size'][$index], $key);
                }
            } else {
                if ('' !== $value['name']) {
                    $this->fileObjects[] = new \York\Request\File($value['name'], $value['type'], $value['tmp_name'], $value['error'], $value['size'], $key);
                }
            }
        }

        return $this;
    }

    /**
     * returns all FileUploadObjects
     *
     * @return \York\Request\File[]
     */
    public function getFiles()
    {
        return $this->fileObjects;
    }

    /**
     * gets all FileUploadObjects with index = $index
     *
     * @param string $index
     *
     * @return \York\Request\File[]
     */
    public function getFileUploadObjectsByIndex($index)
    {
        $return = array();

        foreach ($this->fileObjects as $current) {
            if ($index === $current->uploadIndex) {
                $return[] = $current;
            }
        }

        return $return;
    }

    /**
     * returns the whole original POST data
     *
     * @return array
     */
    public function getRawPOST()
    {
        return $this->rawPOST;
    }

    /**
     * returns the whole original GET data
     *
     * @return array
     */
    public function getRawGET()
    {
        return $this->rawGET;
    }

    /**
     * returns the whole data array
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * returns data for the key
     * post data has higher priority than get data
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws \York\Exception\KeyNotExistsInDataObject
     */
    public function get($key)
    {
        try {
            return $this->getFromPost($key);
        } catch (\York\Exception\KeyNotExistsInDataObject $x) {

        }

        try {
            return $this->getFromGet($key);
        } catch (\York\Exception\KeyNotExistsInDataObject $x) {

        }

        if (true === isset($this->data[$key])) {
            return $this->data[$key];
        }

        throw new \York\Exception\KeyNotExistsInDataObject($key . ' not found in DataObject!');
    }

    /**
     * tries to receive the value for the key
     * returns null if nothing was found
     *
     * @param string    $key
     * @param mixed     $default
     *
     * @return mixed | null
     */
    public function getSafely($key, $default = null)
    {
        try {
            return $this->get($key);
        } catch (\York\Exception\KeyNotExistsInDataObject $x) {
            return $default;
        }
    }

    /**
     * tries to retrieve a value directly from the post array by its key
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws \York\Exception\KeyNotExistsInDataObject
     */
    public function getFromPost($key)
    {
        if (true === isset($this->rawPOST[$key])) {
            return $this->rawPOST[$key];
        }

        throw new \York\Exception\KeyNotExistsInDataObject($key . ' not found in DataObject in POST-Section!');
    }

    /**
     * tries to retrieve a value directly from the get array by its key
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws \York\Exception\KeyNotExistsInDataObject
     */
    public function getFromGet($key)
    {
        if (true === isset($this->rawGET[$key])) {
            return $this->rawGET[$key];
        }

        throw new \York\Exception\KeyNotExistsInDataObject($key . ' not found in DataObject in GET-Section!');
    }

    /**
     * tries to get data via direct object access
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws \York\Exception\KeyNotExistsInDataObject
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * checks, if data was set for $key
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasDataForKey($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * removes the key from post, get and internal data arrays
     *
     * @param string $key
     *
     * @return \York\Request\Data
     */
    public function removeKey($key)
    {
        unset($this->rawGET[$key]);
        unset($this->rawPOST[$key]);
        unset($this->data[$key]);

        return $this;
    }
}
