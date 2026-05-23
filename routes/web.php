<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get("/", fn(): RedirectResponse => redirect("/login"));
Route::get("/login", fn(): View => view("pages.auth.login"))->name("login");

Route::middleware([])->group(function (): void {
    Route::get("/dashboard", fn(): View => view("pages.dashboard"))->name("dashboard");
    Route::get("/project", fn(): View => view("pages.project"))->name("project");
    
    Route::prefix("/team")->group(function (): void {
        Route::get("/", fn(): View => view("pages.team.index"))->name("team");
        Route::get("/create", fn(): View => view("pages.team.member.create"))->name("team.member.create");
        Route::get("/view/{team}", fn(): View => view("pages.team.member.view"))->name("team.member.view");
        Route::get("/edit/{team}", fn(): View => view("pages.team.member.edit"))->name("team.member.edit");
    });
});
