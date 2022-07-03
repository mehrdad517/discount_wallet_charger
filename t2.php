<?php

for ($i = 5000; $i > 0; $i++) {
    exec("php child.php $i worldcup");
}
