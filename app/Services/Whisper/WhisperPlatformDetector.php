<?php

declare(strict_types=1);

namespace App\Services\Whisper;

use Illuminate\Support\Facades\Process;

final class WhisperPlatformDetector
{
    public function getOS(): string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => 'darwin',
            'Windows' => 'windows',
            default => 'linux',
        };
    }

    public function getArch(): string
    {
        $arch = php_uname('m');

        return match ($arch) {
            'arm64', 'aarch64' => 'arm64',
            'x86_64', 'amd64', 'AMD64' => 'x86_64',
            default => $arch,
        };
    }

    public function isWindows(): bool
    {
        return is_windows();
    }

    public function isMacOS(): bool
    {
        return is_mac();
    }

    public function isLinux(): bool
    {
        return is_linux();
    }

    public function hasGpuSupport(): bool
    {
        return match ($this->getOS()) {
            'darwin' => $this->hasMacGpu(),
            'windows' => $this->hasWindowsGpu(),
            'linux' => $this->hasLinuxGpu(),
            default => false,
        };
    }

    private function hasMacGpu(): bool
    {
        $result = Process::run(['sysctl', '-n', 'machdep.cpu.brand_string']);

        return $result->successful() && str_contains(strtolower($result->output()), 'apple');
    }

    private function hasWindowsGpu(): bool
    {
        $result = Process::run(['where', 'nvidia-smi']);

        if ($result->successful()) {
            $nvidiaCheck = Process::run(['nvidia-smi', '-L']);

            return $nvidiaCheck->successful();
        }

        return false;
    }

    private function hasLinuxGpu(): bool
    {
        $result = Process::run(['which', 'nvidia-smi']);

        if ($result->successful()) {
            $nvidiaCheck = Process::run(['nvidia-smi', '-L']);

            return $nvidiaCheck->successful();
        }

        return false;
    }
}
