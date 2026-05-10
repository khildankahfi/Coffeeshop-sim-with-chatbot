<?php

namespace App\Services\Agent\Tools;

use Carbon\Carbon;

class GetOperationalHoursTool
{
    // Jam operasional coffeeshop
    private array $schedule = [
        'Senin'  => ['open' => '07:00', 'close' => '22:00'],
        'Selasa' => ['open' => '07:00', 'close' => '22:00'],
        'Rabu'   => ['open' => '07:00', 'close' => '22:00'],
        'Kamis'  => ['open' => '07:00', 'close' => '22:00'],
        'Jumat'  => ['open' => '07:00', 'close' => '23:00'],
        'Sabtu'  => ['open' => '08:00', 'close' => '23:00'],
        'Minggu' => ['open' => '08:00', 'close' => '21:00'],
    ];

    public function getDefinition(): array
    {
        return [
            'name'        => 'get_operational_hours',
            'description' => 'Cek jam operasional coffeeshop BrewNest hari ini dan seminggu ke depan. Gunakan tool ini ketika pelanggan bertanya soal jam buka, jam tutup, atau apakah coffeeshop sedang buka.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'check_type' => [
                        'type'        => 'string',
                        'enum'        => ['today', 'all', 'status'],
                        'description' => 'today = jam hari ini, all = semua jadwal seminggu, status = apakah sekarang sedang buka.',
                    ],
                ],
                'required' => ['check_type'],
            ],
        ];
    }

    public function execute(array $input): array
    {
        $now      = Carbon::now('Asia/Jakarta');
        $dayNames = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        $today    = $dayNames[$now->format('l')];
        $todaySchedule = $this->schedule[$today];

        $openTime  = Carbon::createFromTimeString($todaySchedule['open'],  'Asia/Jakarta');
        $closeTime = Carbon::createFromTimeString($todaySchedule['close'], 'Asia/Jakarta');
        $isOpen    = $now->between($openTime, $closeTime);

        // Hitung sisa waktu
        $timeInfo = '';
        if ($isOpen) {
            $minutesLeft = $now->diffInMinutes($closeTime);
            $hoursLeft   = floor($minutesLeft / 60);
            $minsLeft    = $minutesLeft % 60;
            $timeInfo    = $hoursLeft > 0
                ? "Tutup dalam {$hoursLeft} jam {$minsLeft} menit"
                : "Tutup dalam {$minsLeft} menit";
        } else {
            if ($now->lt($openTime)) {
                $minutesLeft = $now->diffInMinutes($openTime);
                $hoursLeft   = floor($minutesLeft / 60);
                $minsLeft    = $minutesLeft % 60;
                $timeInfo    = $hoursLeft > 0
                    ? "Buka dalam {$hoursLeft} jam {$minsLeft} menit"
                    : "Buka dalam {$minsLeft} menit";
            } else {
                $timeInfo = "Sudah tutup hari ini";
            }
        }

        $result = [
            'sekarang'          => $now->format('d M Y, H:i') . ' WIB',
            'hari_ini'          => $today,
            'jam_buka_hari_ini' => $todaySchedule['open'] . ' WIB',
            'jam_tutup_hari_ini'=> $todaySchedule['close'] . ' WIB',
            'status'            => $isOpen ? 'BUKA' : 'TUTUP',
            'status_detail'     => $timeInfo,
        ];

        if ($input['check_type'] === 'all') {
            $result['jadwal_mingguan'] = $this->schedule;
        }

        return $result;
    }
}