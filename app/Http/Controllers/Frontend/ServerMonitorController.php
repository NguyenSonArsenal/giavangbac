<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class ServerMonitorController extends Controller
{
    public function index()
    {
        $data = [];

        // ===== DISK INFO =====
        $totalDisk = disk_total_space('/');
        $freeDisk = disk_free_space('/');
        $usedDisk = $totalDisk - $freeDisk;

        $data['disk'] = [
            'total' => $this->formatBytes($totalDisk),
            'used' => $this->formatBytes($usedDisk),
            'free' => $this->formatBytes($freeDisk),
            'percent' => round(($usedDisk / $totalDisk) * 100, 1),
            'total_raw' => $totalDisk,
            'used_raw' => $usedDisk,
            'free_raw' => $freeDisk,
        ];

        // ===== RAM INFO =====
        $data['ram'] = $this->getRamInfo();

        // ===== PROJECT SIZES =====
        $data['projects'] = $this->getProjectSizes('/var/www/html');

        // ===== SYSTEM INFO =====
        $data['system'] = [
            'hostname' => gethostname(),
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'php' => phpversion(),
            'uptime' => $this->getUptime(),
            'server_time' => date('Y-m-d H:i:s'),
        ];

        return view('frontend.server-monitor.index', compact('data'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }

    private function getRamInfo()
    {
        $meminfo = @file_get_contents('/proc/meminfo');
        if (!$meminfo) {
            return ['total' => 'N/A', 'used' => 'N/A', 'free' => 'N/A', 'percent' => 0];
        }

        preg_match('/MemTotal:\s+(\d+)/', $meminfo, $totalMatch);
        preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $availMatch);

        $totalKB = (int) ($totalMatch[1] ?? 0);
        $availKB = (int) ($availMatch[1] ?? 0);
        $usedKB = $totalKB - $availKB;

        return [
            'total' => $this->formatBytes($totalKB * 1024),
            'used' => $this->formatBytes($usedKB * 1024),
            'free' => $this->formatBytes($availKB * 1024),
            'percent' => $totalKB > 0 ? round(($usedKB / $totalKB) * 100, 1) : 0,
            'total_raw' => $totalKB * 1024,
            'used_raw' => $usedKB * 1024,
            'free_raw' => $availKB * 1024,
        ];
    }

    private function getProjectSizes($basePath)
    {
        $projects = [];
        if (!is_dir($basePath)) return $projects;

        $dirs = array_filter(glob($basePath . '/*'), 'is_dir');
        foreach ($dirs as $dir) {
            $size = $this->getDirSize($dir);
            $projects[] = [
                'name' => basename($dir),
                'path' => $dir,
                'size' => $this->formatBytes($size),
                'size_raw' => $size,
            ];
        }

        // Sort by size descending
        usort($projects, function ($a, $b) {
            return $b['size_raw'] - $a['size_raw'];
        });

        return $projects;
    }

    private function getDirSize($dir)
    {
        $output = shell_exec("du -sb " . escapeshellarg($dir) . " 2>/dev/null | cut -f1");
        return (int) trim($output ?: '0');
    }

    private function getUptime()
    {
        $uptime = @file_get_contents('/proc/uptime');
        if (!$uptime) return 'N/A';

        $seconds = (int) explode(' ', $uptime)[0];
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $mins = floor(($seconds % 3600) / 60);

        return "{$days}d {$hours}h {$mins}m";
    }
}
