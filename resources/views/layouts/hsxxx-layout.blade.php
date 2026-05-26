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
  <body class="bg-light">
    <div class="app-container d-flex flex-column min-vh-100">
      <x-fs-header></x-fs-header>
      
      <div class="main-layout d-flex flex-grow-1">
        <x-fs-sidemenu></x-fs-sidemenu>
        
        <main class="content-viewport flex-grow-1 p-4 fade-in">
          @yield("content")
        </main>
      </div>
    </div>

    <x-fs-confirmation-modal></x-fs-confirmation-modal>
  </body>
</html>