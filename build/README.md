# Build Icons

This directory contains application icons for NativePHP/Electron builds.

## Generated Files

- `icon.png` (512x512) - Linux/Electron default icon
- `icon@2x.png` (1024x1024) - High-resolution displays
- `icon-256.png` (256x256) - Windows base icon
- `icon.ico` - Windows application icon (multi-resolution)
- `icon.icns` - macOS application icon (multi-resolution)

## Regenerating Icons

To regenerate all icons from the source WebP image:

```bash
php artisan app:generate-icons
```

This command will:
1. Load `public/images/full-logo-PurrAI.webp` (highest quality source)
2. Generate PNG files at various resolutions
3. Create Windows `.ico` file (requires ImageMagick)
4. Create macOS `.icns` file (requires icnsutils)

## Requirements

For full icon generation on Linux:

```bash
sudo apt install imagemagick icnsutils
```

## Icon Specifications

### Windows (.ico)
- Multi-resolution: 256, 128, 64, 48, 32, 16 pixels
- Format: ICO
- Location: `build/icon.ico`

### macOS (.icns)
- Multi-resolution: 1024, 512, 256, 128, 64, 32, 16 pixels
- Format: ICNS
- Location: `build/icon.icns`

### Linux (.png)
- Resolution: 512x512 (standard) or 1024x1024 (high-res)
- Format: PNG
- Location: `build/icon.png`

## Electron Builder

Electron Builder automatically uses these icons during the build process:
- Looks for `build/icon.{ico,icns,png}` based on target platform
- No additional configuration needed in `electron-builder.mjs`

## Menu Bar Icons (macOS)

For macOS menu bar/tray icons:
- `public/IconTemplate.png` (16x16)
- `public/IconTemplate@2x.png` (32x32)

These are used at runtime by the `Dock::icon()` method.
