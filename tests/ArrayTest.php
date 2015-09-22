<?php

class ArrayTest extends PHPUnit_Framework_TestCase {
    public function array_has_provider() {
        return [
            [true, ['foo' => 'bar'], 'foo'],
            [false, ['foo' => 'bar'], 'bar'],
            [true, ['foo' => ['bar' => 'baz']], 'foo.bar'],
            [false, ['foo' => ['bar' => 'baz']], 'foo.baz'],
            [true, ['foo' => ['bar' => ['baz' => 'yolo']]], 'foo.bar.baz'],
            [false, ['foo' => ['bar' => ['baz' => 'yolo']]], 'foo.bar.yolo'],
        ];
    }

    public function array_get_provider() {
        return [
            [null, ['foo' => 'bar'], null, null],
            ['bar', ['foo' => 'bar'], 'foo', null],
            ['foo', ['foo' => 'bar'], 'bar', 'foo'],
            ['baz', ['foo' => ['bar' => 'baz']], 'foo.bar', null],
            ['bar', ['foo' => ['bar' => 'baz']], 'foo.baz', 'bar'],
            ['yolo', ['foo' => ['bar' => ['baz' => 'yolo']]], 'foo.bar.baz', null],
            ['baz', ['foo' => ['bar' => ['baz' => 'yolo']]], 'foo.bar.yolo', 'baz'],
        ];
    }

    public function array_set_provider() {
        return [
            [null, null, 'foo'],
            ['foo', 'foo', 'foo'],
            ['bar', 'foo.bar', 'bar'],
            ['baz', 'foo.bar.baz', 'baz'],
        ];
    }

    public function array_remove_provider() {
        return [
            ['foo'],
            ['foo.bar'],
            ['foo.bar.baz']
        ];
    }

    public function array_dot_provider() {
        return [
            [['foo' => 'bar'], ['foo' => 'bar']],
            [['foo.bar' => 'baz'], ['foo' => ['bar' => 'baz']]],
            [['foo.bar.baz' => 'yolo'], ['foo' => ['bar' => ['baz' => 'yolo']]]],
        ];
    }

    public function array_extend_provider() {
        return [
            [
                ['foo' => 'bar', 'bar' => 'bar', [1, 2, 3]],
                ['foo' => 'foo', [1, 2, 3]],
                ['foo' => 'bar', 'bar' => 'bar']
            ],
            [
                ['foo' => ['bar' => 'baz'], [1, 2, 3, 'foo' => 'bar', 'yolo' => 'swag']],
                ['foo' => ['bar' => ['baz' => 'yolo']], [1, 'yolo' => 'swag']],
                ['foo' => ['bar' => 'baz'], [1, 2, 3, 'foo' => 'bar']],
            ],
            [
                [0 => 'yolo', 1 => 'bar', 'bar' => ['bar' => ['baz' => 'swag']], 'baz' => ['foo' => 'bar'], 2 => [1, 3]],
                [0 => 'foo', 1 => 'bar', 'baz' => ['foo' => 'bar'], 2 => [2, 3]],
                [0 => 'yolo', 'bar' => ['bar' => ['baz' => 'swag']], 2 => [1]],
            ],
        ];
    }

    public function array_extend_distinct_provider() {
        return [
            [
                ['foo' => 'bar', 'bar' => 'bar', [1, 2, 3]],
                ['foo' => 'foo', [1, 2, 3]],
                ['foo' => 'bar', 'bar' => 'bar']
            ],
            [
                ['foo' => ['bar' => 'baz'], [1, 2, 3, 'foo' => 'bar', 'yolo' => 'swag']],
                ['foo' => ['bar' => ['baz' => 'yolo']], [1, 'yolo' => 'swag']],
                ['foo' => ['bar' => 'baz'], [1, 2, 3, 'foo' => 'bar']],
            ],
            [
                [0 => 'yolo', 1 => 'bar', 'bar' => ['bar' => ['baz' => 'swag']], 'baz' => ['foo' => 'bar'], 2 => [1]],
                [0 => 'foo', 1 => 'bar', 'baz' => ['foo' => 'bar'], 2 => [2, 3]],
                [0 => 'yolo', 'bar' => ['bar' => ['baz' => 'swag']], 2 => [1]],
            ],
        ];
    }

    /**
     * @dataProvider array_has_provider
     */
    public function test_array_has($expected, $array, $path) {
        $this->assertEquals($expected, array_has($array, $path));
    }

    /**
     * @dataProvider array_get_provider
     */
    public function test_array_get($expected, $array, $path, $default) {
        $this->assertEquals($expected, array_get($array, $path, $default));
    }

    /**
     * @dataProvider array_set_provider
     */
    public function test_array_set($expected, $path, $value) {
        $array = [];
        $this->assertFalse(array_has($array, $path));
        array_set($array, $path, $value);
        $this->assertEquals($expected, array_get($array, $path));
    }

    /**
     * @dataProvider array_remove_provider
     */
    public function test_array_remove($path) {
        $array = [];
        array_set($array, $path, 'foo');
        $this->assertTrue(array_has($array, $path));
        array_remove($array, $path);
        $this->assertFalse(array_has($array, $path));
    }

    /**
     * @dataProvider array_dot_provider
     */
    public function test_array_dot($expected, $array) {
        $this->assertEquals($expected, array_dot($array));
    }

    /**
     * @dataProvider array_extend_provider
     */
    public function test_array_extend($expected, $array1, $array2) {
        $this->assertEquals($expected, array_extend($array1, $array2));
    }

    public function test_array_extend_many() {
        $expected = [
            'foo' => 'bar', 'bar' => 'foo', 'baz' => 'foo', 'yolo' => 'swag'
        ];

        $array1 = ['foo' => 'bar'];
        $array2 = ['bar' => 'foo'];
        $array3 = ['baz' => 'foo'];
        $array4 = ['yolo' => 'swag'];

        $this->assertEquals($expected, array_extend($array1, $array2, $array3, $array4));
    }

    /**
     * @dataProvider array_extend_distinct_provider
     */
    public function test_array_extend_distinct($expected, $array1, $array2) {
        $this->assertEquals($expected, array_extend_distinct($array1, $array2));
    }
}
