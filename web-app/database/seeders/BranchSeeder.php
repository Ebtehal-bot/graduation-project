<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // قائمة بالمحافظات اليمنية الرئيسية لتعبئتها تلقائياً
        $governorates = [
            'صنعاء', 
            'عدن', 
            'تعز', 
            'الحديدة', 
            'حضرموت', 
            'إب', 
            'ذمار', 
            'مأرب'
        ];

        foreach ($governorates as $gov) {
            // استخدام firstOrCreate لمنع التكرار في حال نفذت الأمر أكثر من مرة
            Branch::firstOrCreate([
                'governorate' => $gov,
            ], [
                'name' => "فرع محافظة " . $gov,
                'phone' => '777777777',
                'address' => 'المركز الرئيسي للمحافظة - الشارع العام',
            ]);
        }
    }
}