<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserLoginActivityExport implements WithMultipleSheets
{
    protected ?string $period;
    protected ?string $role;
    protected ?string $search;
    protected ?string $tab;

    public function __construct(?string $period = 'this_month', ?string $role = 'all', ?string $search = null, ?string $tab = 'active')
    {
        $this->period = $period ?? 'this_month';
        $this->role = $role ?? 'all';
        $this->search = $search;
        $this->tab = $tab ?? 'active';
    }

    public function sheets(): array
    {
        $startDate = null;
        $endDate = Carbon::now();

        switch ($this->period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                break;
            case 'all_time':
                $startDate = null;
                $endDate = null;
                break;
            case 'this_month':
            default:
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                break;
        }

        $periodLabels = [
            'today' => 'Hari Ini',
            'last_7_days' => '7 Hari Terakhir',
            'this_month' => 'Bulan Ini',
            'last_30_days' => '30 Hari Terakhir',
            'all_time' => 'Semua Waktu',
        ];
        $periodLabel = $periodLabels[$this->period] ?? 'Bulan Ini';

        // Query Active Users
        $activeQuery = User::whereIn('role', ['mahasiswa', 'dosen', 'kaprodi', 'admin'])
            ->when($this->role && $this->role !== 'all', fn($q) => $q->where('role', $this->role))
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                       ->orWhere('identifier', 'like', "%{$this->search}%");
                });
            })
            ->withCount([
                'activityLogs as period_logins_count' => function ($q) use ($startDate, $endDate) {
                    $q->where('activity', 'Login');
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                },
                'activityLogs as total_logins_count' => function ($q) {
                    $q->where('activity', 'Login');
                }
            ])
            ->orderByDesc('period_logins_count')
            ->orderByDesc('total_logins_count')
            ->orderByDesc('last_login_at')
            ->get();

        // Query Inactive Users
        $inactiveQuery = User::whereIn('role', ['mahasiswa', 'dosen', 'kaprodi', 'admin'])
            ->when($this->role && $this->role !== 'all', fn($q) => $q->where('role', $this->role))
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                       ->orWhere('identifier', 'like', "%{$this->search}%");
                });
            })
            ->where(function ($q) {
                $q->whereNull('last_login_at')
                  ->orWhere('last_login_at', '<', Carbon::now()->subDays(30));
            })
            ->withCount([
                'activityLogs as total_logins_count' => function ($q) {
                    $q->where('activity', 'Login');
                }
            ])
            ->orderByRaw('last_login_at IS NULL DESC')
            ->orderBy('last_login_at', 'asc')
            ->get();

        return [
            new ActiveUsersSheet($activeQuery, $periodLabel),
            new InactiveUsersSheet($inactiveQuery),
        ];
    }
}

class ActiveUsersSheet extends DefaultValueBinder implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    protected Collection $users;
    protected string $periodLabel;
    private int $rank = 0;

    public function __construct(Collection $users, string $periodLabel)
    {
        $this->users = $users;
        $this->periodLabel = $periodLabel;
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'C') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        return $this->users;
    }

    public function title(): string
    {
        return 'Peringkat Keaktifan Login';
    }

    public function headings(): array
    {
        return [
            'PERINGKAT',
            'NAMA LENGKAP',
            'NIM / NIDN',
            'EMAIL',
            'PERAN',
            'STATUS ONLINE',
            "LOGIN ({$this->periodLabel})",
            'TOTAL LOGIN ALL-TIME',
            'LOGIN TERAKHIR',
        ];
    }

    public function map($user): array
    {
        $this->rank++;
        return [
            $this->rank,
            $user->name,
            $user->identifier ?: '-',
            $user->email,
            ucfirst($user->role),
            $user->is_online ? 'ONLINE' : 'OFFLINE',
            $user->period_logins_count . ' kali',
            $user->total_logins_count . ' kali',
            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : 'Belum Pernah',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEA580C'], // Orange SIBIMA
                ],
            ],
        ];
    }
}

class InactiveUsersSheet extends DefaultValueBinder implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    protected Collection $users;
    private int $row = 0;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'C' || $cell->getColumn() === 'G') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        return $this->users;
    }

    public function title(): string
    {
        return 'User Belum & Jarang Login';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'NIM / NIDN',
            'EMAIL',
            'PERAN',
            'STATUS KEAKTIFAN',
            'NO WHATSAPP',
            'LOGIN TERAKHIR',
            'TOTAL LOGIN',
        ];
    }

    public function map($user): array
    {
        $this->row++;
        $status = 'Belum Pernah Login';
        if ($user->last_login_at) {
            $days = $user->last_login_at->diffInDays(now());
            $status = "Tidak Aktif ({$days} Hari Lalu)";
        }

        return [
            $this->row,
            $user->name,
            $user->identifier ?: '-',
            $user->email,
            ucfirst($user->role),
            $status,
            $user->phone_number ?: '-',
            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : 'Belum Pernah',
            $user->total_logins_count . ' kali',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE11D48'], // Rose
                ],
            ],
        ];
    }
}
