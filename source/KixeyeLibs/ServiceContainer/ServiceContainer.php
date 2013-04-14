<?php

namespace KixeyeLibs\ServiceContainer;

/**
 * Classes which extend this service container can serve
 * as the principle creation and configuration point of a projects
 * service assets.
 *
 * It's worth noting that this container always returns the same
 * instance of a given index.  That is, there is always singleton
 * behavior for the assets set and fetching with this container.
 *
 * In addition to storing and returning values, this container will
 * automatically evaluate any closures that are stored in it, the
 * first time they are fetched.
 *
 * One use case is to instantiate an object of this class directly,
 * and use the array access interface to set and get elements from
 * its store.
 *
 * Another alternative is to extend this class, and define
 * collection elements in the extended class's constructor.
 */
class ServiceContainer implements \ArrayAccess
{
    /**
     * The collection of services of which this container is aware.
     *
     * @var array
     */
    protected $services = [];

    /**
     * Does the given array index exist in this collection?
     *
     * @param  mixed $service_name The array index under question
     *
     * @return boolean the existence result
     */
    public function offsetExists($service_name)
    {
        return isset($this->services[$service_name]);
    }

    /**
     * Fetch a given index from this collection.
     *
     * @param  mixed $service_name The array index to fetch
     *
     * @throws \Exception whenthe given index does not exist in this collection
     *
     * @return mixed the value stored under this index
     */
    public function offsetGet($service_name)
    {
        if (!$this->offsetExists($service_name)) {
            throw new \Exception("The requested service ('{$service_name}') is not defined in this Service Container.");
        }

        if (is_callable($this->services[$service_name]) && !is_string($this->services[$service_name])) {
            $this->services[$service_name] = $this->services[$service_name]();
        }

        return $this->services[$service_name];
    }

    /**
     * Store a given value at the given index in this collection.
     *
     * @param  mixed $service_name The index under which to store the given value
     * @param  mixed $value        Any value suitable for storage in this collection
     *
     * @throws \Exception when a null index is used.
     *
     * @return void
     */
    public function offsetSet($service_name, $value)
    {
        if (is_null($service_name)) {
            throw new \Exception('Null indices are not allowed in Service Containers.');
        }

        $this->services[$service_name] = $value;
    }

    /**
     * Clear a given index from the collection.
     *
     * @param  mixed $service_name The index in question
     *
     * @return void
     */
    public function offsetUnset($service_name)
    {
        unset($this->services[$service_name]);
    }
}
