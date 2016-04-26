<?php

class Socialstore_Payment_Options
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct(array $data=array())
    {
        $this->_data = $data;
    }

    /**
     * Set value by key
     *
     * @param string $key
     * @param string $value
     * @return Socialstore_Payment_Info
     */
    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * Get value by key
     *
     * @param $key
     * @return string | null
     */
    public function get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Check if key exists
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Map info data keys to alternative keys
     *
     * Example:
     * array(
     *  'info_data_key' => 'new_key'
     * )
     * @param array $keys
     */
    public function map(array $keys)
    {
        $result = array();
        foreach ($keys as $key => $mapKey) {
            if ($this->has($key)) {
                $result[$mapKey] = $this->get($key);
            }
        }
        return $result;
    }

    /**
     * Import data from source based on map
     *
     * @param array $source source array
     * @param array $map source keys map
     */
    public function import(array $source, array $map = array())
    {
        if (empty($map)) {
            $this->_data = $source;
        } else {
            foreach ($map as $key => $infoKey) {
                if (array_key_exists($key, $source)) {
                    $this->_data[$infoKey] = $source[$key];
                }
            }
        }
        return $this;
    }
	
	public function toArray(){
		return (array)$this->_data;
	}
}