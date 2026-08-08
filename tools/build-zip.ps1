# Build lean WordPress theme zip (excludes design refs, tools, VCS).
$ErrorActionPreference = "Stop"
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$root = Split-Path $PSScriptRoot -Parent
if (-not (Test-Path (Join-Path $root "style.css"))) {
	$root = $PSScriptRoot
	if (-not (Test-Path (Join-Path $root "style.css"))) {
		$root = "e:\cevizsoft\baydemir-theme"
	}
}

$distDir = Join-Path $root "dist"
$zipPath = Join-Path $distDir "baydemir-theme.zip"
New-Item -ItemType Directory -Force -Path $distDir | Out-Null

$excludeDirs = @(
	".git",
	"dist",
	"node_modules",
	".cursor",
	"agent-transcripts",
	".vs",
	"references",
	"tools"
)

$excludeFiles = @(
	".DS_Store",
	"Thumbs.db",
	"references.zip",
	"details.txt",
	".gitignore",
	".gitattributes",
	"composer.json",
	"composer.lock",
	"package.json",
	"package-lock.json"
)

$excludePatterns = @(
	'^tools\\',
	'^references\\',
	'^references\.zip$',
	'^details\.txt$',
	'\\lights-windows-(debug|overlay)\.png$'
)

function Should-Exclude([string]$fullPath, [string]$rootPath) {
	$rel = $fullPath.Substring($rootPath.Length).TrimStart('\', '/')
	$parts = $rel -split '[\\/]'
	foreach ($d in $excludeDirs) {
		if ($parts -contains $d) { return $true }
	}
	$leaf = Split-Path $fullPath -Leaf
	if ($excludeFiles -contains $leaf) { return $true }
	foreach ($pat in $excludePatterns) {
		if ($rel -match $pat) { return $true }
	}
	return $false
}

if (Test-Path $zipPath) { Remove-Item $zipPath -Force }

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)
$count = 0
$total = 0L

Get-ChildItem -Path $root -Recurse -File -Force | Where-Object { -not (Should-Exclude $_.FullName $root) } | ForEach-Object {
	$rel = $_.FullName.Substring($root.Length).TrimStart('\', '/')
	$entryName = ("baydemir-theme/" + ($rel -replace '\\', '/'))
	[void][System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
		$zip,
		$_.FullName,
		$entryName,
		[System.IO.Compression.CompressionLevel]::Optimal
	)
	$count++
	$total += $_.Length
}

$zip.Dispose()

$zipMB = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
$srcMB = [math]::Round($total / 1MB, 2)
Write-Output "Packed $count files ($srcMB MB source) -> $zipPath ($zipMB MB)"
