<?php

if ( ! function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     *
     * @return mixed
     */
    function array_get($array, $key, $default = null) {
        if ($key === null) {
            return null;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if ( ! function_exists('array_has')) {
    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     *
     * @return bool
     */
    function array_has($array, $key) {
        if (empty($array) || $key === null) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }
}

if ( ! function_exists('array_set')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     *
     * @return array
     */
    function array_set(&$array, $key, $value) {
        if ($key === null) {
            return null;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if ( ! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if ( ! function_exists('array_remove')) {
    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array $array
     * @param  array|string $keys
     *
     * @return void
     */
    function array_remove(&$array, $keys) {
        $original = &$array;

        foreach ((array) $keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                }
            }

            unset($array[array_shift($parts)]);

            // clean up after each pass
            $array = &$original;
        }
    }
}

if ( ! function_exists('array_dot')) {
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array $array
     * @param  string $prepend
     *
     * @return array
     */
    function array_dot($array, $prepend = '') {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}

if ( ! function_exists('array_extend')) {
    /**
     * Extend one array with another.
     *
     * @param array $arrays
     *
     * @return array
     */
    function array_extend(array $arrays) {
        $merged = [];

        foreach (func_get_args() as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && array_has($merged, $key) && is_array($merged[$key])) {
                    $merged[$key] = array_extend($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}

if ( ! function_exists('array_extend_distinct')) {
    /**
     * Extend one array with another. Non associative arrays will not be merged
     * but rather replaced.
     *
     * @param array $arrays
     *
     * @return array
     */
    function array_extend_distinct(array $arrays) {
        $merged = [];

        foreach (func_get_args() as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) &&
                    array_has($merged, $key) && is_array($merged[$key]) &&
                    array_keys($value) !== range(0, count($value) - 1)) {
                    $merged[$key] = array_extend($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}

