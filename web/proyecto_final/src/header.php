<?php
global $pagina_actual;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="dist\output.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img\favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title><?php  echo $pagina_actual[0]["page_name"] ?></title>
</head>
<body>