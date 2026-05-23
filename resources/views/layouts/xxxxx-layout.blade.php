<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="route-name" content="{{ request()->route()?->getName() ?? "" }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config("app.name", "FlowSync") }} @hasSection("page-title") - @yield("page-title") @endif</title>
    
    @vite(["resources/scss/app.scss", "resources/js/app.js"])
  </head>
  <body>
    @yield("content")
  </body>
</html>