<?php

function base_url(string $path = ''): string
{
    $base = '';
    return $base . '/' . ltrim($path, '/');
}