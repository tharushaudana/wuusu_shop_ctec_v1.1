<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ActivitiesExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithEvents
{
    protected $audits, $title;

    public function __construct($audits, $title)
    {
        $this->audits = $audits;
        $this->title = $title;
    }

    public function collection()
    {
        //return $this->audits;

        return new Collection(); //### return empty collection
    }

    public function headings(): array
    {
        /*return [
            'ID',
            'User ID',
            'Event',
            'Target',
            'Target ID',
            'Old Values',
            'New Values',
            'IP Address',
            'Date & Time'
        ];*/

        return [];
    }

    public function map($audit): array
    {
        /*return [
            $audit->id,
            $audit->user_id,
            $audit->event,
            str_replace('App\Models\\', '', $audit->auditable_type),
            $audit->auditable_id,
            empty($audit->old_values) ? '-' : $audit->old_values,
            empty($audit->new_values) ? '-' : $audit->new_values,
            $audit->ip_address,
            $audit->created_at,
        ];*/

        return [];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->setTitle($event);
                $this->setHeadings($event);
                $this->setRows($event);
            },
        ];
    }

    //### Private functions

    private function setTitle(&$event) {
        $event->sheet->mergeCells('A1:I1'); // Merge cells A1 to E1
        $event->sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $event->sheet->getStyle('A1')->getAlignment()->setVertical('center');
        $event->sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setARGB('FF000000');
        $event->sheet->getCell('A1')->setValue($this->title);
        $event->sheet->getRowDimension('1')->setRowHeight(40);
    }

    private function setHeadings(&$event) {
        $event->sheet->setCellValue('A2', 'ID');
        $event->sheet->setCellValue('B2', 'User ID');
        $event->sheet->setCellValue('C2', 'Event');
        $event->sheet->setCellValue('D2', 'Target');
        $event->sheet->setCellValue('E2', 'Target ID');
        $event->sheet->setCellValue('F2', 'Old Values');
        $event->sheet->setCellValue('G2', 'New Values');
        $event->sheet->setCellValue('H2', 'IP Address');
        $event->sheet->setCellValue('I2', 'Date & Time');
    }

    private function setRows(&$event) {
        $i = 3;

        foreach ($this->audits as $audit) {
            $event->sheet->setCellValue('A'.$i, $audit->id);
            $event->sheet->setCellValue('B'.$i, $audit->user_id);
            $event->sheet->setCellValue('C'.$i, $audit->event);
            $event->sheet->setCellValue('D'.$i, str_replace('App\Models\\', '', $audit->auditable_type));
            $event->sheet->setCellValue('E'.$i, $audit->auditable_id);
            $event->sheet->setCellValue('F'.$i, empty($audit->old_values) ? '-' : $audit->old_values);
            $event->sheet->setCellValue('G'.$i, empty($audit->new_values) ? '-' : $audit->new_values);
            $event->sheet->setCellValue('H'.$i, $audit->ip_address);
            $event->sheet->setCellValue('I'.$i, $audit->created_at);

            $i++;
        }
    }
}