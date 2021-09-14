<?php
namespace MkdCore\Lib;

/**
 * interface PostTypeInterface
 * @package MkdCore\Lib;
 */
interface PostTypeInterface {
    /**
     * @return string
     */
    public function getBase();

    /**
     * Registers custom post type with WordPress
     */
    public function register();
}