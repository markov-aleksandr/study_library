<?php

namespace Application\Core;

class View
{
    public function generate($contentView, $templateView, $data = null)
    {
        include 'application/views/' . $templateView;
    }

    public function create($contentView, $data = null)
    {
        include 'application/views/' . $contentView;
    }
}