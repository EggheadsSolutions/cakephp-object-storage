<?php
declare(strict_types=1);

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;

const TMP = __DIR__ . '/tmp/';
const CONFIG = __DIR__ . '/config/';

Configure::config('default', new PhpConfig());

try {
    Configure::load('app_local', 'default');
} catch (Exception $e) {
    echo 'Необходимо добавить app_local.php';
}
