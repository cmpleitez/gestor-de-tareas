@echo off
REM ============================================================
REM  sync-rules.cmd
REM  Copia este archivo a la RAIZ de cualquier proyecto.
REM  Doble clic = actualiza el CLAUDE.md de ESE proyecto
REM  con las reglas globales del repo estandares.
REM  No hace push: tu controlas el git de cada PC.
REM ============================================================
setlocal

REM Carpeta donde esta este .bat = la raiz del proyecto
set "PROY=%~dp0"
if "%PROY:~-1%"=="\" set "PROY=%PROY:~0,-1%"

REM El repo estandares es hermano del proyecto bajo \www
set "SCRIPT=%PROY%\..\estandares\ia\sync-reglas.ps1"

if not exist "%SCRIPT%" (
  echo No encuentro el script de sync en:
  echo   %SCRIPT%
  echo Revisa que el repo "estandares" este junto a tus proyectos.
  echo.
  pause
  exit /b 1
)

powershell -ExecutionPolicy Bypass -File "%SCRIPT%" -Proyecto "%PROY%"

echo.
echo ------------------------------------------------------------
echo Listo. Ahora haz tu commit y push de este proyecto.
echo ------------------------------------------------------------
pause
