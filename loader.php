<?php
declare(strict_types=1);

foreach (glob(__DIR__ . '/inc/*.php') as $file) {
    require_once $file;
}