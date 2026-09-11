<?php
function beautifyHTML($html) {
    $html = preg_replace('/>\s+</', '><', $html); // Collapse whitespace between tags
    $tokens = preg_split('/(<[^>]+>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    $indent = 0;
    $output = "";
    $inHead = false;
    $inlineTags = ['meta', 'link', 'title', 'base'];

    foreach ($tokens as $token) {
        $token = trim($token);
        if ($token === '') continue;

        preg_match('/<\/?([a-zA-Z0-9\-]+)/', $token, $matches);
        $tagName = strtolower($matches[1] ?? '');

        // Detect entering or exiting <head>
        if ($tagName === 'head') {
            $inHead = strpos($token, '</') === false;
        }

        // Reduce indent for closing tags
        if (preg_match('/^<\/[^>]+>$/', $token)) {
            $indent--;
        }

        // Format line with correct indentation
        if ($inHead && in_array($tagName, $inlineTags)) {
            $line = "  " . $token . "\n"; // Fixed 2-space indent for inline tags inside head
        } else {
            $line = str_repeat("  ", $indent) . $token . "\n";
        }

        $output .= $line;

        // Increase indent for opening tags (not self-closing or inline)
        if (preg_match('/^<[^\/!][^>]*[^\/]>$/', $token) && !in_array($tagName, $inlineTags)) {
            $indent++;
        }
    }

    return trim($output);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HTML Beautifier</title>
  <style>
    body { font-family: sans-serif; padding: 20px; }
    textarea { width: 100%; height: 200px; font-family: monospace; }
    input[type="submit"] { padding: 10px 20px; margin-top: 10px; }
    .output { margin-top: 20px; }
  </style>
</head>
<body>

<h2>Beautify Your HTML Code</h2>
<form method="post">
  <label for="html_input">Paste HTML Code:</label><br>
  <textarea id="html_input" name="html_input"><?php echo isset($_POST['html_input']) ? htmlspecialchars($_POST['html_input']) : ''; ?></textarea><br>
  <input type="submit" value="Beautify">
</form>

<?php if (!empty($_POST['html_input'])): ?>
  <div class="output">
    <h3>Formatted HTML:</h3>
    <textarea readonly><?php echo htmlspecialchars(beautifyHTML($_POST['html_input'])); ?></textarea>
  </div>
<?php endif; ?>

</body>
</html>
