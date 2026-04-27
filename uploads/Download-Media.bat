@echo off
REM Doppelklicke diese Datei, um alle Bilder und Videos herunterzuladen.
cd /d "%~dp0"
powershell.exe -ExecutionPolicy Bypass -File "%~dp0Download-Media.ps1"
