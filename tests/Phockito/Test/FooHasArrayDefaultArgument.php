<?php

namespace Phockito\Test;


class FooHasArrayDefaultArgument
{
    /**
     * @param array $a
     * @return null
     */
    function Foo($a = [1, 2, 3])
    {
    }
}