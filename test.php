<?php
echo "<h1>Path Test</h1>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";

// Test if CSS file exists
$css_path = __DIR__ . '/assets/css/style.css';
echo "<p>CSS file path: " . $css_path . "</p>";
echo "<p>CSS file exists: " . (file_exists($css_path) ? 'YES' : 'NO') . "</p>";

// Test if we can read it
if (file_exists($css_path)) {
    echo "<p>CSS file is readable: " . (is_readable($css_path) ? 'YES' : 'NO') . "</p>";
}

// Output a simple link to test
echo '<p><a href="assets/css/style.css">Click to test CSS file directly</a></p>';
?>