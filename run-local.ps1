param(
    [int]$Port = 8081,
    [string]$AdminUser = $env:ADMIN_USER,
    [string]$AdminPassword = $env:ADMIN_PASSWORD,
    [string]$AdminRole = $(if ($env:ADMIN_ROLE) { $env:ADMIN_ROLE } else { "ADMIN" }),
    [switch]$NoBrowser
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$pidFile = Join-Path $root ".local-server.pid"
$outLog = Join-Path $root "local-server.out.log"
$errLog = Join-Path $root "local-server.err.log"

function Find-Maven {
    $fromPath = Get-Command mvn.cmd -ErrorAction SilentlyContinue
    if ($fromPath) {
        return $fromPath.Source
    }

    $cached = Get-ChildItem -Path "$env:USERPROFILE\.m2\wrapper\dists" -Recurse -Filter mvn.cmd -ErrorAction SilentlyContinue |
        Sort-Object FullName -Descending |
        Select-Object -First 1

    if ($cached) {
        return $cached.FullName
    }

    throw "No encontre mvn.cmd. Ejecuta Maven una vez o instala Maven y vuelve a correr este script."
}

if (Test-Path $pidFile) {
    $oldPid = Get-Content $pidFile -ErrorAction SilentlyContinue
    if ($oldPid -and (Get-Process -Id $oldPid -ErrorAction SilentlyContinue)) {
        Write-Host "Deteniendo servidor local anterior (PID $oldPid)..."
        & taskkill.exe /PID $oldPid /T /F | Out-Null
    }
    Remove-Item $pidFile -Force -ErrorAction SilentlyContinue
}

$mvn = Find-Maven
$jdk21 = "C:\Program Files\Eclipse Adoptium\jdk-21.0.7.6-hotspot"
if (Test-Path $jdk21) {
    $env:JAVA_HOME = $jdk21
    $env:PATH = "$jdk21\bin;$env:PATH"
}

$env:PORT = "$Port"
$env:SPRING_DATASOURCE_URL = "jdbc:h2:mem:rinconcito;MODE=MySQL;DATABASE_TO_LOWER=TRUE;DB_CLOSE_DELAY=-1"
$env:SPRING_DATASOURCE_DRIVER_CLASS_NAME = "org.h2.Driver"
$env:SPRING_DATASOURCE_USERNAME = "sa"
$env:SPRING_DATASOURCE_PASSWORD = ""
$env:SPRING_JPA_HIBERNATE_DDL_AUTO = "create-drop"
if ($AdminUser -and $AdminPassword) {
    $env:APP_ADMIN_BOOTSTRAP_ENABLED = "true"
    $env:ADMIN_USER = $AdminUser
    $env:ADMIN_PASSWORD = $AdminPassword
    $env:ADMIN_ROLE = $AdminRole
} else {
    $env:APP_ADMIN_BOOTSTRAP_ENABLED = "false"
    Remove-Item Env:\ADMIN_USER -ErrorAction SilentlyContinue
    Remove-Item Env:\ADMIN_PASSWORD -ErrorAction SilentlyContinue
    Remove-Item Env:\ADMIN_ROLE -ErrorAction SilentlyContinue
}
$env:OPINION_EMAIL_ENABLED = "false"

Remove-Item -LiteralPath $outLog, $errLog -Force -ErrorAction SilentlyContinue

Write-Host "Iniciando Rinconcito Marino en http://localhost:$Port ..."
$proc = Start-Process -FilePath $mvn `
    -ArgumentList @("-Dspring-boot.run.useTestClasspath=true", "spring-boot:run") `
    -WorkingDirectory $root `
    -RedirectStandardOutput $outLog `
    -RedirectStandardError $errLog `
    -PassThru `
    -WindowStyle Hidden

$proc.Id | Set-Content $pidFile

$deadline = (Get-Date).AddSeconds(90)
$ready = $false
while ((Get-Date) -lt $deadline) {
    if ($proc.HasExited) {
        break
    }

    try {
        $response = Invoke-WebRequest -Uri "http://localhost:$Port/" -UseBasicParsing -TimeoutSec 3
        if ($response.StatusCode -eq 200) {
            $ready = $true
            break
        }
    } catch {
        Start-Sleep -Seconds 2
    }
}

if (-not $ready) {
    Write-Host "No arranco correctamente. Ultimas lineas del log:"
    Get-Content $outLog -Tail 60 -ErrorAction SilentlyContinue
    Get-Content $errLog -Tail 60 -ErrorAction SilentlyContinue
    exit 1
}

if (-not $NoBrowser) {
    Start-Process "http://localhost:$Port/"
}

Write-Host "Listo: http://localhost:$Port/"
Write-Host "Logs: $outLog"
Write-Host "Para detenerlo: .\stop-local.cmd"
