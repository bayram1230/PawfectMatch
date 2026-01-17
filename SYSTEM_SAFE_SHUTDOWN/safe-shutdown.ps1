Write-Host ""
Write-Host "Running safe shutdown check..." -ForegroundColor Cyan
Write-Host ""

php bin/console app:system:safe-shutdown

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "SYSTEM READY FOR SHUTDOWN" -ForegroundColor Green
    Write-Host ""
    exit 0
}
else {
    Write-Host ""
    Write-Host "DO NOT SHUT DOWN" -ForegroundColor Red
    Write-Host ""
    exit 1
}
