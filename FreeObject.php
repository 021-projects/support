<?php

namespace O21\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class FreeObject extends Collection
{
    protected array $objects = [];

    protected array $getters = [];

    protected array $dates   = [];

    /**
     * Magically access collection data.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->getPropertyValue($property);
    }

    /**
     * Magically map to an object class (if exists) and return data.
     *
     * @param $originalProperty
     * @param null $default
     *
     * @return mixed
     */
    protected function getPropertyValue($originalProperty, $default = null)
    {
        $property = Str::snake($originalProperty);

        $value = $this->offsetExists($property)
            ? $this->items[$property]
            : value($default);

        $objectClass = __NAMESPACE__.'\\'.ucfirst($property);
        if (class_exists($objectClass) && is_array($value)) {

            if (! isset($this->objects[$property])) {
                $this->objects[$property] = new $objectClass($value);
            }

            return $this->objects[$property];
        }

        if ($this->isDateProperty($originalProperty)) {
            return $this->asDateTime($value);
        }

        $getterMethod = 'get' . Str::camel($property);

        if (method_exists($this, $getterMethod)) {

            if (! isset($this->getters[$property])) {
                $this->getters[$property] = $this->$getterMethod($value);
            }

            return $this->getters[$property];
        }

        return $value;
    }

    protected function isDateProperty($property): bool
    {
        return in_array($property, $this->dates);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Carbon|null
     */
    protected function asDateTime($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof CarbonInterface) {
            return Date::instance($value);
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof \DateTimeInterface) {
            return Date::parse(
                $value->format('Y-m-d H:i:s.u'), $value->getTimezone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        }

        return Date::parse($value);
    }

    /**
     * Get an item from the collection by key.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = parent::get($key, $default);

        if (is_array($value)) {
            return $this->getPropertyValue($key, $default);
        }

        return $value;
    }

    /**
     * Magic method to get properties dynamically.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (! Str::startsWith($name, 'get')) {
            return false;
        }
        $property = substr($name, 3);

        return $this->getPropertyValue($property);
    }
}
