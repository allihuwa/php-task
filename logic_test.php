<?php
for ($i = 1; $i <= 100; $i++) {
    $output = "";

    if ($i % 3 === 0) {
        $output .= "foo";
    }

    if ($i % 5 === 0) {
        $output .= "bar";
    }

    if (empty($output)) {
        $output = $i;
    }

    echo $output . "\n";
  }
?>