<?php
class Test {

    public function __construct()
    {
        Events::register('test', array($this, 'string_return'));
    }

    public function string_return()
    {
        return 'I returned a string. Cakes and Pies!';
    }
}