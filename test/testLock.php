<?php
    require_once dirname(__FILE__).'/'.'../CacheLock.php';
    
    $lock = new CacheLock(__FILE__);
    $lock->lock();
    echo "In lock"."<br />";
    $lock->unlock();
    echo "Out lock"."<br />";

?>
