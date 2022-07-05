<?php

for ($i = 0; $i < 5000; $i++) {
    exec("php child.php $i worldcup");
}
