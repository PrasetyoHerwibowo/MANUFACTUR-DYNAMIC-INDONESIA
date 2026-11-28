@echo off
setlocal
title Ngrok Tunneling - Manufactur Dynamic Indonesia

set NGROK_BIN="E:\laragon\bin\ngrok\ngrok.exe"

if not exist %NGROK_BIN% (
    where ngrok >nul 2>nul
    if %errorlevel% equ 0 (
        set NGROK_BIN=ngrok
    ) else (
        echo [ERROR] ngrok.exe tidak ditemukan di E:\laragon\bin\ngrok\ngrok.exe atau di PATH sistem.
        pause
        exit /b 1
    )
)

echo ========================================================
echo    NGROK TUNNEL TESTING - MANUFACTUR DYNAMIC INDONESIA
echo ========================================================
echo Tips: Agar UI di HP tidak berantakan, aset CSS/JS wajib di-build.
echo.
set /p DO_BUILD="Apakah Anda ingin build aset Vite sekarang? (y/n, default: y): "
if /i not "%DO_BUILD%"=="n" (
    echo Menjalankan build aset Vite...
    call npm run build
)

echo.
echo ========================================================
echo Pilih target aplikasi yang sedang berjalan:
echo   [1] php artisan serve - Port 8000 [DEFAULT]
echo   [2] Laragon Virtual Host / Apache - Port 80
echo ========================================================
set /p TARGET_CHOICE="Pilihan Anda (1 atau 2, default: 1): "

if "%TARGET_CHOICE%"=="2" goto TARGET_APACHE
goto TARGET_SERVE

:TARGET_APACHE
echo.
echo Menghubungkan Ngrok ke Laragon Apache Port 80...
echo Tekan Ctrl+C untuk berhenti.
%NGROK_BIN% http --host-header=rewrite 80
goto END

:TARGET_SERVE
echo.
echo Menghubungkan Ngrok ke php artisan serve Port 8000...
echo Tekan Ctrl+C untuk berhenti.
%NGROK_BIN% http 8000
goto END

:END
