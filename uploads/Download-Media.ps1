# Download-Media.ps1
# Lädt alle Bilder/Videos aus urls.txt mit Originalnamen in den aktuellen Ordner herunter.
# Ausführen:  Rechtsklick auf die Datei -> "Mit PowerShell ausführen"
#       oder: powershell -ExecutionPolicy Bypass -File .\Download-Media.ps1

$ErrorActionPreference = "Continue"
$ProgressPreference = "SilentlyContinue"

# In den Ordner wechseln, in dem dieses Skript liegt
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $scriptDir

$urlsFile = Join-Path $scriptDir "urls.txt"
if (-not (Test-Path $urlsFile)) {
    Write-Host "ERROR: urls.txt nicht gefunden in $scriptDir" -ForegroundColor Red
    exit 1
}

$urls = Get-Content $urlsFile | Where-Object { $_.Trim() -ne "" }
Write-Host "Gefunden: $($urls.Count) Dateien zum Herunterladen" -ForegroundColor Cyan
Write-Host "Zielordner: $scriptDir" -ForegroundColor Cyan
Write-Host ""

$batchSize = 8
$success = 0
$failed = @()
$skipped = 0
$total = $urls.Count
$counter = 0

# Batch-weise parallel herunterladen
for ($i = 0; $i -lt $urls.Count; $i += $batchSize) {
    $batch = $urls[$i..([Math]::Min($i + $batchSize - 1, $urls.Count - 1))]
    $jobs = @()

    foreach ($url in $batch) {
        $fileName = [System.IO.Path]::GetFileName(([Uri]$url).LocalPath)
        $target   = Join-Path $scriptDir $fileName

        if (Test-Path $target) {
            $script:skipped++
            $script:counter++
            Write-Host "[$counter/$total] SKIP (existiert) $fileName" -ForegroundColor DarkGray
            continue
        }

        $jobs += Start-Job -ScriptBlock {
            param($u, $t, $n)
            try {
                Invoke-WebRequest -Uri $u -OutFile $t -UseBasicParsing -ErrorAction Stop
                return @{ ok=$true; name=$n; url=$u }
            } catch {
                if (Test-Path $t) { Remove-Item $t -Force -ErrorAction SilentlyContinue }
                return @{ ok=$false; name=$n; url=$u; err=$_.Exception.Message }
            }
        } -ArgumentList $url, $target, $fileName
    }

    foreach ($job in $jobs) {
        $result = Receive-Job -Job $job -Wait -AutoRemoveJob
        $counter++
        if ($result.ok) {
            $success++
            Write-Host "[$counter/$total] OK   $($result.name)" -ForegroundColor Green
        } else {
            $failed += $result
            Write-Host "[$counter/$total] FAIL $($result.name) :: $($result.err)" -ForegroundColor Red
        }
    }
}

Write-Host ""
Write-Host "Fertig." -ForegroundColor Cyan
Write-Host "  Erfolgreich:   $success" -ForegroundColor Green
Write-Host "  Uebersprungen: $skipped" -ForegroundColor DarkGray
Write-Host "  Fehler:        $($failed.Count)" -ForegroundColor $(if ($failed.Count -gt 0) { 'Red' } else { 'Green' })

if ($failed.Count -gt 0) {
    Write-Host ""
    Write-Host "Fehlgeschlagene URLs:" -ForegroundColor Yellow
    $failed | ForEach-Object { Write-Host "  $($_.url)" }
}

Write-Host ""
Write-Host "Druecke eine Taste zum Schliessen..." -ForegroundColor DarkGray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
