<?php
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    echo opcache_reset() ? 'success' : 'fail';
} else {
    echo 'error';
}
echo PHP_EOL;