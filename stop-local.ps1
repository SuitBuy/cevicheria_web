$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$pidFile = Join-Path $root ".local-server.pid"

if (-not (Test-Path $pidFile)) {
    Write-Host "No hay servidor local registrado."
    exit 0
}

$pidValue = Get-Content $pidFile -ErrorAction SilentlyContinue
if ($pidValue -and (Get-Process -Id $pidValue -ErrorAction SilentlyContinue)) {
    & taskkill.exe /PID $pidValue /T /F | Out-Null
    Write-Host "Servidor local detenido (PID $pidValue)."
} else {
    Write-Host "El servidor local ya no estaba activo."
}

Remove-Item $pidFile -Force -ErrorAction SilentlyContinue
