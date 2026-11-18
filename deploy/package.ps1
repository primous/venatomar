# Builds deploy\venatomar-deploy.zip, ready to upload to Hostinger.
# Usage (PowerShell, from the project folder):  powershell -ExecutionPolicy Bypass -File deploy\package.ps1
# The zip contains production dependencies only, compiled assets, and NO .env, logs or tests.

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$stage = Join-Path $env:TEMP 'venatomar-deploy'
$zip = Join-Path $PSScriptRoot 'venatomar-deploy.zip'

Set-Location $root
Write-Host '1/4 Compiling assets (npm run build)...'
npm run build
if ($LASTEXITCODE -ne 0) { throw 'npm run build failed' }

Write-Host '2/4 Copying files...'
if (Test-Path $stage) { Remove-Item -Recurse -Force $stage }
New-Item -ItemType Directory -Force $stage | Out-Null
# robocopy exit codes below 8 mean success.
robocopy $root $stage /E /NFL /NDL /NJH /NJS /NP `
    /XD node_modules vendor tests deploy .git .claude .idea .vscode `
        "$root\storage\logs" "$root\storage\framework\cache\data" "$root\storage\framework\sessions" "$root\storage\framework\views" `
    /XF .env .env.backup .phpunit.result.cache *.log *.zip | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy failed ($LASTEXITCODE)" }
foreach ($d in 'storage\logs', 'storage\framework\cache\data', 'storage\framework\sessions', 'storage\framework\views') {
    New-Item -ItemType Directory -Force (Join-Path $stage $d) | Out-Null
    Set-Content -Path (Join-Path $stage "$d\.gitignore") -Value "*`n!.gitignore" -Encoding ascii
}

Write-Host '3/4 Installing production dependencies (composer --no-dev)...'
Set-Location $stage
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
if ($LASTEXITCODE -ne 0) { throw 'composer install failed' }
Remove-Item -Force (Join-Path $stage 'bootstrap\cache\*.php') -ErrorAction SilentlyContinue

Write-Host '4/4 Creating the zip...'
Set-Location $root
if (Test-Path $zip) { Remove-Item -Force $zip }
# Windows' built-in tar writes "/" separators (Compress-Archive on PowerShell 5.1 writes "\", which breaks on Linux).
tar -a -c -f $zip -C $stage .
if ($LASTEXITCODE -ne 0) { throw 'zip creation failed' }
Remove-Item -Recurse -Force $stage
Write-Host "Done: $zip"
